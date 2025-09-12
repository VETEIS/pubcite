<?php

namespace App\Http\Controllers\Traits;

use App\Models\Request;
use Illuminate\Support\Facades\Log;

trait DraftSessionManager
{
    /**
     * Get or validate draft session for a user
     * 
     * @param int $userId
     * @param string $type (Publication or Citation)
     * @return array ['draft' => Request|null, 'isValid' => bool, 'sessionKey' => string]
     */
    protected function getDraftSession($userId, $type)
    {
        try {
            $sessionKey = "draft_{$type}_{$userId}";
            $sessionData = session($sessionKey);
            
            // If no session, return null
            if (!$sessionData) {
                return [
                    'draft' => null,
                    'isValid' => false,
                    'sessionKey' => $sessionKey
                ];
            }
            
            // Validate session data structure
            if (!$this->isValidSessionData($sessionData)) {
                Log::warning('Invalid session data structure detected', [
                    'user_id' => $userId,
                    'type' => $type,
                    'session_data' => $sessionData,
                    'session_key' => $sessionKey
                ]);
                
                session()->forget($sessionKey);
                return [
                    'draft' => null,
                    'isValid' => false,
                    'sessionKey' => $sessionKey
                ];
            }
            
            // Handle both old format (just ID) and new format (with expiry)
            if (is_array($sessionData)) {
                $draftId = $sessionData['draft_id'] ?? null;
                $expiresAt = $sessionData['expires_at'] ?? null;
            } else {
                // Legacy format - just the draft ID
                $draftId = $sessionData;
                $expiresAt = null;
            }
        } catch (\Exception $e) {
            return $this->handleSessionError($userId, $type, $e);
        }
        
        // Check if session has expired
        if ($expiresAt && now()->isAfter($expiresAt)) {
            session()->forget($sessionKey);
            Log::info('Draft session expired and cleared', [
                'user_id' => $userId,
                'type' => $type,
                'expired_at' => $expiresAt,
                'session_key' => $sessionKey
            ]);
            
            return [
                'draft' => null,
                'isValid' => false,
                'sessionKey' => $sessionKey
            ];
        }
        
        // Validate that the draft still exists and belongs to the user
        $draft = Request::where('id', $draftId)
            ->where('user_id', $userId)
            ->where('type', $type)
            ->where('status', 'draft')
            ->first();
        
        if (!$draft) {
            // Session is invalid, clear it
            session()->forget($sessionKey);
            Log::warning('Invalid draft session cleared', [
                'user_id' => $userId,
                'type' => $type,
                'session_draft_id' => $draftId,
                'session_key' => $sessionKey
            ]);
            
            return [
                'draft' => null,
                'isValid' => false,
                'sessionKey' => $sessionKey
            ];
        }
        
        return [
            'draft' => $draft,
            'isValid' => true,
            'sessionKey' => $sessionKey
        ];
    }
    
    /**
     * Set draft session for a user with expiry
     * 
     * @param int $userId
     * @param string $type
     * @param int $draftId
     * @param int $expiryHours Hours until session expires (default: 24)
     * @return bool
     */
    protected function setDraftSession($userId, $type, $draftId, $expiryHours = 24)
    {
        try {
            $sessionKey = "draft_{$type}_{$userId}";
            $expiresAt = now()->addHours($expiryHours);
            
            $sessionData = [
                'draft_id' => $draftId,
                'expires_at' => $expiresAt,
                'created_at' => now()
            ];
            
            session([$sessionKey => $sessionData]);
            
            Log::info('Draft session set with expiry', [
                'user_id' => $userId,
                'type' => $type,
                'draft_id' => $draftId,
                'expires_at' => $expiresAt,
                'session_key' => $sessionKey
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to set draft session', [
                'user_id' => $userId,
                'type' => $type,
                'draft_id' => $draftId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Clear draft session for a user
     * 
     * @param int $userId
     * @param string $type
     * @return bool
     */
    protected function clearDraftSession($userId, $type)
    {
        try {
            $sessionKey = "draft_{$type}_{$userId}";
            session()->forget($sessionKey);
            
            Log::info('Draft session cleared', [
                'user_id' => $userId,
                'type' => $type,
                'session_key' => $sessionKey
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear draft session', [
                'user_id' => $userId,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Find existing draft for user (fallback when session is invalid)
     * 
     * @param int $userId
     * @param string $type
     * @return Request|null
     */
    protected function findExistingDraft($userId, $type)
    {
        $draft = Request::where('user_id', $userId)
            ->where('type', $type)
            ->where('status', 'draft')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($draft) {
            // Set session for future requests
            $this->setDraftSession($userId, $type, $draft->id);
        }
        
        return $draft;
    }
    
    /**
     * Handle session corruption gracefully
     * 
     * @param int $userId
     * @param string $type
     * @param \Exception $exception
     * @return array
     */
    protected function handleSessionError($userId, $type, $exception)
    {
        $sessionKey = "draft_{$type}_{$userId}";
        
        // Clear corrupted session
        session()->forget($sessionKey);
        
        Log::error('Session corruption detected and cleared', [
            'user_id' => $userId,
            'type' => $type,
            'session_key' => $sessionKey,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Try to find existing draft as fallback
        $draft = $this->findExistingDraft($userId, $type);
        
        return [
            'draft' => $draft,
            'isValid' => false,
            'sessionKey' => $sessionKey,
            'error' => 'Session corrupted, cleared and fallback attempted'
        ];
    }
    
    /**
     * Validate session data structure
     * 
     * @param mixed $sessionData
     * @return bool
     */
    protected function isValidSessionData($sessionData)
    {
        if (is_array($sessionData)) {
            return isset($sessionData['draft_id']) && 
                   isset($sessionData['expires_at']) && 
                   is_numeric($sessionData['draft_id']);
        }
        
        // Legacy format - just numeric ID
        return is_numeric($sessionData);
    }
}
