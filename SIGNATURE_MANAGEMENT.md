# Signature Management System

This document describes the signature management system implemented for the PubCite application, which allows users to upload, manage, and securely store their personal handwritten signatures.

## Overview

The signature management system provides a secure, privacy-focused way for users to upload and manage their digital signatures. All signatures are stored privately in S3 with strict access controls, ensuring only the owner can view their signatures.

## Security Features

### Privacy & Access Control
- **Owner-only access**: Only the signature owner can view, edit, or delete their signatures
- **404 responses**: Non-owners receive 404 (not 403) to prevent enumeration
- **No admin access**: Even administrators cannot view raw signature images
- **Policy-based authorization**: Uses Laravel policies for consistent access control

### Storage Security
- **Private S3 storage**: All signatures stored with private visibility
- **Server-side encryption**: Supports SSE-KMS for additional security
- **Temporary URLs**: Short-lived (2-minute) URLs for secure access
- **No public ACLs**: Bucket configured to block public access

### File Security
- **MIME type validation**: Server-side validation using `finfo`
- **Image re-encoding**: All images re-encoded server-side to strip metadata
- **Size limits**: Configurable file size and dimension limits
- **PNG only**: Only PNG images are accepted for security
- **Hash verification**: SHA-256 hashes stored for integrity checking

### Rate Limiting
- **Upload limits**: 10 attempts per minute per user/IP
- **URL generation**: 30 attempts per minute per user/IP
- **Confirmation limits**: 10 attempts per minute per user/IP

## Architecture

### Database Schema

#### Signatures Table
```sql
CREATE TABLE signatures (
    id UUID PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    label VARCHAR(120) NULL,
    object_key VARCHAR UNIQUE NOT NULL,
    mime_type VARCHAR(50) NULL,
    width_px INTEGER NULL,
    height_px INTEGER NULL,
    hash_sha256 VARCHAR(64) NULL,
    encrypted_meta BINARY NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

#### Signature Audits Table
```sql
CREATE TABLE signature_audits (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    signature_id UUID NULL,
    user_id BIGINT NULL,
    action VARCHAR NOT NULL,
    ip VARCHAR NULL,
    user_agent VARCHAR NULL,
    event_id VARCHAR NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Key Components

#### Models
- `Signature`: Main model with soft deletes and relationships
- `SignatureAudit`: Audit logging for all signature operations
- `User`: Extended with signatures relationship

#### Services
- `SignatureStorageService`: Handles S3 operations, image processing, and security
- `SignaturePolicy`: Authorization policies for signature access

#### Controllers
- `SignatureController`: RESTful API endpoints for signature management
- `SignaturesIndex` (Livewire): Interactive UI component

#### Jobs
- `HardDeleteSignatures`: Background job for permanent deletion after retention period

## API Endpoints

### Web Routes
- `GET /signatures` - List user's signatures
- `POST /signatures/presign-upload` - Generate upload credentials
- `POST /signatures/confirm` - Confirm uploaded signature
- `GET /signatures/{id}/url` - Get temporary access URL
- `PATCH /signatures/{id}` - Update signature metadata
- `DELETE /signatures/{id}` - Soft delete signature

### Livewire Component
- `livewire:signatures-index` - Full UI for signature management

## Configuration

### Environment Variables
```env
# Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_KMS_KEY_ID=your-kms-key-id  # Optional for SSE-KMS

# Signature Limits
MAX_SIGNATURE_BYTES=1000000  # 1MB
SIGNATURE_MAX_DIMENSIONS=2000
SIGNATURE_RETENTION_DAYS=90
```

### S3 Bucket Configuration
1. Create private S3 bucket
2. Enable server-side encryption (SSE-KMS recommended)
3. Block public access
4. Configure bucket policy to deny anonymous access
5. Set up lifecycle policies for cost optimization

## Usage

### Upload Flow
1. User initiates upload with label
2. System generates presigned URL and creates pending record
3. Client uploads directly to S3
4. Client confirms upload with object key
5. System validates, processes, and stores signature
6. Thumbnail generated and stored
7. Audit event logged

### Access Flow
1. User requests signature access
2. System validates ownership
3. Temporary URL generated (2-minute expiry)
4. Audit event logged
5. User accesses signature via temporary URL

### Deletion Flow
1. User requests signature deletion
2. System validates ownership
3. Signature soft deleted (hidden from user)
4. S3 objects deleted
5. Audit event logged
6. Background job permanently deletes after retention period

## Testing

Run the test suite:
```bash
php artisan test --filter=SignatureManagementTest
```

Key test scenarios:
- User can upload, view, update, and delete own signatures
- Users cannot access other users' signatures
- Rate limiting is enforced
- Validation works correctly
- Audit logging functions properly

## Maintenance

### Scheduled Tasks
Add to your cron job:
```bash
# Daily signature cleanup
0 2 * * * cd /path/to/app && php artisan signatures:cleanup
```

### Monitoring
Monitor these metrics:
- Upload success/failure rates
- Rate limit hits
- Storage usage
- Audit log volume
- Job queue performance

## Security Considerations

### Production Checklist
- [ ] S3 bucket configured with private access
- [ ] Server-side encryption enabled
- [ ] Rate limiting configured
- [ ] Audit logging enabled
- [ ] Background jobs configured
- [ ] Monitoring and alerting set up
- [ ] Regular security reviews scheduled

### Incident Response
1. Monitor audit logs for suspicious activity
2. Review rate limit violations
3. Check for unauthorized access attempts
4. Verify S3 bucket security settings
5. Review signature access patterns

## Troubleshooting

### Common Issues

**Upload failures**
- Check S3 credentials and permissions
- Verify file size and format limits
- Review rate limiting settings

**Access denied errors**
- Verify user authentication
- Check signature ownership
- Review policy configuration

**Storage issues**
- Monitor S3 bucket capacity
- Check encryption key status
- Verify bucket permissions

### Debug Commands
```bash
# Check signature storage
php artisan tinker
>>> App\Models\Signature::count()

# Review audit logs
php artisan tinker
>>> App\Models\SignatureAudit::latest()->take(10)->get()

# Test S3 connectivity
php artisan tinker
>>> Storage::disk('s3')->put('test.txt', 'test')
```

## Future Enhancements

- SVG signature support with sanitization
- Advanced image processing options
- Bulk signature operations
- Signature templates and categories
- Integration with document signing workflows
- Advanced analytics and reporting
