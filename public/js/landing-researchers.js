class LandingResearchers {
    constructor() {
        this.apiUrl = '/api/researchers';
        this.container = document.getElementById('researchers-container');
        this.loadingElement = document.getElementById('researchers-loading');
        this.emptyElement = document.getElementById('researchers-empty');
        this.errorElement = document.getElementById('researchers-error');
        
        if (!this.container) {
            return;
        }
        
        this.init();
    }
    
    init() {
        // Load researchers on page load
        this.load();
        
        // Set up Turbo support
        document.addEventListener('turbo:load', () => {
            this.load();
        });
    }
    
    async load() {
        if (!this.container) return;
        
        this.showLoading();
        
        try {
            const response = await fetch(`${this.apiUrl}?t=${Date.now()}`, {
                cache: 'no-cache',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.researchers && data.researchers.length > 0) {
                this.renderResearchers(data.researchers);
            } else {
                this.showEmpty();
            }
            
        } catch (error) {
            this.showError();
        }
    }
    
    showLoading() {
        this.hideAllStates();
        if (this.loadingElement) {
            this.loadingElement.classList.remove('hidden');
        }
    }
    
    showEmpty() {
        this.hideAllStates();
        if (this.emptyElement) {
            this.emptyElement.classList.remove('hidden');
        }
    }
    
    showError() {
        this.hideAllStates();
        if (this.errorElement) {
            this.errorElement.classList.remove('hidden');
        }
    }
    
    hideAllStates() {
        [this.loadingElement, this.emptyElement, this.errorElement].forEach(el => {
            if (el) el.classList.add('hidden');
        });
    }
    
    renderResearchers(researchers) {
        this.hideAllStates();
        
        // Clear existing researcher cards (but keep state elements)
        const existingCards = this.container.querySelectorAll('.researcher-card');
        existingCards.forEach(card => card.remove());
        
        // Also remove any hardcoded content that might still be there
        const hardcodedContent = this.container.querySelectorAll('div:not(#researchers-loading):not(#researchers-empty):not(#researchers-error)');
        hardcodedContent.forEach(el => {
            if (!el.id && !el.classList.contains('researcher-card')) {
                el.remove();
            }
        });
        
        if (researchers.length === 0) {
            this.showEmpty();
            return;
        }
        
        researchers.forEach((researcher, index) => {
            const card = this.createResearcherCard(researcher, index);
            this.container.appendChild(card);
        });
    }
    
    createResearcherCard(researcher, index) {
        const card = document.createElement('div');
        // Compact width (w-52 = 208px) - more compact card design
        card.className = 'flex-shrink-0 w-52 bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 hover:border-maroon-300 hover:-translate-y-1 overflow-hidden researcher-card';
        card.setAttribute('data-name', researcher.name || '');
        card.setAttribute('data-tags', (researcher.research_areas || []).join(' '));
        
        // Get background color classes
        const bgClasses = this.getBackgroundClasses(researcher.background_color || 'maroon');
        const statusBadgeClasses = this.getStatusBadgeClasses(researcher.status_badge || 'Active');
        
        // Create research areas HTML (show only first one with counter if more exist)
        const allResearchAreas = researcher.research_areas || [];
        const researchAreasHtml = this.createResearchAreaCardHtml(allResearchAreas);
        
        // Create top area (compact image or fallback background with icon)
        // Reduced height (120px) for more compact design
        const topAreaHtml = researcher.photo_path
            ? `<div class="h-[120px] w-full overflow-hidden bg-gray-100 flex-shrink-0">
                    <img src="/storage/${researcher.photo_path}" alt="${this.escapeHtml(researcher.name || '')}" class="w-full h-full object-cover object-center">
               </div>`
            : `<div class="h-[120px] ${bgClasses.background} w-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-12 h-12 ${bgClasses.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 0 18 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
               </div>`;
        
        // Truncate bio properly with CSS
        const bio = researcher.bio || '';
        const truncatedBio = bio.length > 100 ? bio.substring(0, 100) + '...' : bio;
        
        card.innerHTML = `
            <div class="relative">
                ${topAreaHtml}
                <div class="absolute top-2 right-2">
                    <span class="${statusBadgeClasses} text-white text-xs px-2 py-0.5 rounded-full font-medium shadow-sm">${this.escapeHtml(researcher.status_badge || 'Active')}</span>
                </div>
            </div>
            <div class="p-4 flex flex-col flex-1">
                <div class="flex-1 min-h-0">
                    <h3 class="text-base font-semibold text-gray-900 mb-1 line-clamp-1" title="${this.escapeHtml((researcher.prefix ? researcher.prefix + ' ' : '') + (researcher.name || ''))}">${this.escapeHtml(this.formatCardName(researcher))}</h3>
                    <p class="text-xs text-gray-500 mb-2 line-clamp-1">${this.escapeHtml(researcher.title || '')}</p>
                    ${researchAreasHtml ? `<div class="flex flex-wrap gap-1.5 mb-3">${researchAreasHtml}</div>` : ''}
                    <p class="text-xs text-gray-600 leading-relaxed line-clamp-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">${this.escapeHtml(truncatedBio)}</p>
                </div>
                <button type="button" onclick="openResearcherModal(this)" data-researcher="${JSON.stringify(researcher).replace(/"/g, '&quot;')}" class="inline-flex items-center justify-center w-full px-3 py-2 mt-3 text-xs font-medium rounded-md bg-maroon-600 text-white hover:bg-maroon-700 transition-colors">View Profile</button>
            </div>
        `;
        
        return card;
    }
    
    getBackgroundClasses(color) {
        const colorMap = {
            'maroon': {
                background: 'bg-gradient-to-br from-maroon-100 to-maroon-200',
                avatar: 'bg-maroon-300',
                icon: 'text-maroon-600'
            },
            'blue': {
                background: 'bg-gradient-to-br from-blue-100 to-blue-200',
                avatar: 'bg-blue-300',
                icon: 'text-blue-600'
            },
            'green': {
                background: 'bg-gradient-to-br from-green-100 to-green-200',
                avatar: 'bg-green-300',
                icon: 'text-green-600'
            },
            'purple': {
                background: 'bg-gradient-to-br from-purple-100 to-purple-200',
                avatar: 'bg-purple-300',
                icon: 'text-purple-600'
            },
            'orange': {
                background: 'bg-gradient-to-br from-orange-100 to-orange-200',
                avatar: 'bg-orange-300',
                icon: 'text-orange-600'
            },
            'teal': {
                background: 'bg-gradient-to-br from-teal-100 to-teal-200',
                avatar: 'bg-teal-300',
                icon: 'text-teal-600'
            },
            'rose': {
                background: 'bg-gradient-to-br from-rose-100 to-rose-200',
                avatar: 'bg-rose-300',
                icon: 'text-rose-600'
            }
        };
        
        return colorMap[color] || colorMap['maroon'];
    }
    
    getStatusBadgeClasses(status) {
        const statusMap = {
            'Active': 'bg-green-500',
            'Research': 'bg-blue-500',
            'Innovation': 'bg-purple-500',
            'Leadership': 'bg-orange-500',
            'Collaboration': 'bg-teal-500',
            'Excellence': 'bg-rose-500'
        };
        
        return statusMap[status] || 'bg-green-500';
    }
    
    // Create research area HTML for card display (first one only with counter)
    createResearchAreaCardHtml(researchAreas) {
        if (!Array.isArray(researchAreas) || researchAreas.length === 0) {
            return '';
        }
        
        const firstArea = researchAreas[0];
        const remainingCount = researchAreas.length - 1;
        
        const colorClasses = [
            'bg-blue-100 text-blue-800',
            'bg-green-100 text-green-800',
            'bg-purple-100 text-purple-800',
            'bg-orange-100 text-orange-800',
            'bg-red-100 text-red-800',
            'bg-teal-100 text-teal-800',
            'bg-indigo-100 text-indigo-800',
            'bg-pink-100 text-pink-800',
            'bg-yellow-100 text-yellow-800',
            'bg-cyan-100 text-cyan-800',
            'bg-emerald-100 text-emerald-800',
            'bg-violet-100 text-violet-800'
        ];
        
        const colorClass = colorClasses[0];
        const displayArea = firstArea.length > 20 ? firstArea.substring(0, 20) + '...' : firstArea;
        
        let html = `<span class="${colorClass} text-xs px-1.5 py-0.5 rounded-full" title="${this.escapeHtml(firstArea)}">${this.escapeHtml(displayArea)}</span>`;
        
        // Add counter badge if there are more research areas
        if (remainingCount > 0) {
            html += ` <span class="bg-gray-100 text-gray-700 text-xs px-1.5 py-0.5 rounded-full font-medium">(+${remainingCount})</span>`;
        }
        
        return html;
    }
    
    createResearchAreasHtml(researchAreas) {
        if (!Array.isArray(researchAreas) || researchAreas.length === 0) {
            return '';
        }
        
        const colorClasses = [
            'bg-blue-100 text-blue-800',
            'bg-green-100 text-green-800',
            'bg-purple-100 text-purple-800',
            'bg-orange-100 text-orange-800',
            'bg-red-100 text-red-800',
            'bg-teal-100 text-teal-800',
            'bg-indigo-100 text-indigo-800',
            'bg-pink-100 text-pink-800',
            'bg-yellow-100 text-yellow-800',
            'bg-cyan-100 text-cyan-800',
            'bg-emerald-100 text-emerald-800',
            'bg-violet-100 text-violet-800'
        ];
        
        return researchAreas.map((area, index) => {
            const colorClass = colorClasses[index % colorClasses.length];
            // Truncate long research area names
            const displayArea = area.length > 15 ? area.substring(0, 15) + '...' : area;
            return `<span class="${colorClass} text-xs px-1.5 py-0.5 rounded-full" title="${this.escapeHtml(area)}">${this.escapeHtml(displayArea)}</span>`;
        }).join('');
    }
    
    // Format name for landing page cards: prefix + surname only
    formatCardName(researcher) {
        const prefix = researcher.prefix ? researcher.prefix.trim() + ' ' : '';
        const name = researcher.name || '';
        
        if (!name) {
            return '';
        }
        
        // Split name into parts
        const nameParts = name.trim().split(/\s+/).filter(part => part.length > 0);
        
        if (nameParts.length === 0) {
            return '';
        }
        
        // Get surname (last name)
        const surname = nameParts[nameParts.length - 1];
        
        // Return prefix + surname
        return prefix + surname;
    }
    
    formatDisplayName(researcher, maxLength = 30) {
        const prefix = researcher.prefix ? researcher.prefix.trim() + ' ' : '';
        const name = researcher.name || '';
        
        if (!name) {
            return '';
        }
        
        // Split name into parts
        const nameParts = name.trim().split(/\s+/).filter(part => part.length > 0);
        
        if (nameParts.length === 0) {
            return '';
        }
        
        // If only one word, return prefix + name
        if (nameParts.length === 1) {
            const result = prefix + nameParts[0];
            return result.length <= maxLength ? result : result.substring(0, maxLength - 3) + '...';
        }
        
        // Get first name, middle names, and last name
        const firstName = nameParts[0];
        const lastName = nameParts[nameParts.length - 1];
        const middleParts = nameParts.slice(1, -1);
        
        // Build middle initial(s) - take first character of each middle name
        let middleInitials = '';
        if (middleParts.length > 0) {
            middleInitials = ' ' + middleParts.map(part => {
                // If part already ends with a period (like "T."), keep it as is
                if (part.endsWith('.')) {
                    return part;
                }
                // Otherwise, take first character and add period
                return part.charAt(0).toUpperCase() + '.';
            }).join(' ');
        }
        
        // Build formatted name: prefix + first name + middle initial(s) + last name
        const formattedName = `${prefix}${firstName}${middleInitials} ${lastName}`;
        
        // If it fits, return it
        if (formattedName.length <= maxLength) {
            return formattedName;
        }
        
        // If too long, try without middle initials
        const withoutMiddle = `${prefix}${firstName} ${lastName}`;
        if (withoutMiddle.length <= maxLength) {
            return withoutMiddle;
        }
        
        // If still too long, truncate first name
        const availableForFirst = maxLength - prefix.length - lastName.length - 1; // 1 for space
        if (availableForFirst > 0) {
            return `${prefix}${firstName.substring(0, availableForFirst)} ${lastName}`;
        }
        
        // If even last name is too long, truncate it
        const availableForLast = maxLength - prefix.length - 3; // 3 for "..."
        if (availableForLast > 0) {
            return prefix + lastName.substring(0, availableForLast) + '...';
        }
        
        // Fallback
        return prefix || name.substring(0, maxLength - 3) + '...';
    }
    
    truncateName(name, maxLength = 20) {
        if (!name || typeof name !== 'string') {
            return '';
        }
        
        const trimmed = name.trim();
        if (trimmed.length <= maxLength) {
            return trimmed;
        }
        
        const parts = trimmed.split(/\s+/);
        
        // If only one word, truncate normally
        if (parts.length === 1) {
            return trimmed.substring(0, maxLength - 3) + '...';
        }
        
        // Get first name and last name
        const firstName = parts[0];
        const lastName = parts[parts.length - 1];
        
        // If first + last name fits, use that
        const firstLast = `${firstName} ${lastName}`;
        if (firstLast.length <= maxLength) {
            // If there are middle names, add an ellipsis
            if (parts.length > 2) {
                return `${firstName}...${lastName}`;
            }
            return firstLast;
        }
        
        // If first + last is too long, truncate the first name
        const availableForFirstName = maxLength - lastName.length - 4; // 4 for "... "
        if (availableForFirstName > 0) {
            const truncatedFirst = firstName.substring(0, availableForFirstName);
            return `${truncatedFirst}...${lastName}`;
        }
        
        // If even last name is too long, truncate it
        return lastName.substring(0, maxLength - 3) + '...';
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Global function to open researcher modal
window.openResearcherModal = function(button) {
    let researcherData = {};
    
    try {
        const dataAttr = button.getAttribute('data-researcher') || '{}';
        // Decode HTML entities if present
        const decoded = dataAttr.replace(/&#39;/g, "'").replace(/&quot;/g, '"');
        researcherData = JSON.parse(decoded);
    } catch (error) {
        return;
    }
    
    // Get modal elements
    const modal = document.getElementById('researcherProfileModal');
    const modalPhoto = document.getElementById('modal-researcher-photo');
    const modalName = document.getElementById('modal-researcher-name');
    const modalStatus = document.getElementById('modal-researcher-status');
    const modalEmailHeader = document.getElementById('modal-researcher-email-header');
    const modalEmailText = document.getElementById('modal-researcher-email-text');
    const modalBio = document.getElementById('modal-researcher-bio');
    const modalBioSection = document.getElementById('modal-researcher-bio-section');
    const modalNameStatusSeparator = document.getElementById('modal-researcher-separator');
    const modalAreasHeader = document.getElementById('modal-researcher-areas-header');
    const scopusBtn = document.getElementById('modal-scopus-btn');
    const orcidBtn = document.getElementById('modal-orcid-btn');
    const wosBtn = document.getElementById('modal-wos-btn');
    const googleScholarBtn = document.getElementById('modal-google-scholar-btn');
    
    if (!modal) return;
    
    // Helper function to escape HTML
    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    };
    
    // Helper function to format name with prefix
    const formatFullName = (prefix, name) => {
        if (!name) return '';
        return prefix ? `${prefix} ${name}` : name;
    };
    
    // Set photo (smaller size for compact header)
    if (researcherData.photo_path) {
        modalPhoto.innerHTML = `<img src="/storage/${researcherData.photo_path}" alt="${escapeHtml(researcherData.name || '')}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-2 border-white shadow-md">`;
    } else {
        modalPhoto.innerHTML = `<div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-2 border-white bg-white/20 flex items-center justify-center shadow-md">
            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 0 18 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>`;
    }
    
    // Set name with prefix
    const fullName = formatFullName(researcherData.prefix || '', researcherData.name || '');
    modalName.textContent = fullName;
    
    // Set status badge
    let hasBadge = false;
    if (researcherData.status_badge) {
        const statusBadgeClasses = {
            'Active': 'bg-green-500',
            'Research': 'bg-blue-500',
            'Innovation': 'bg-purple-500',
            'Leadership': 'bg-orange-500',
            'Collaboration': 'bg-teal-500',
            'Excellence': 'bg-rose-500'
        };
        const badgeClass = statusBadgeClasses[researcherData.status_badge] || 'bg-green-500';
        modalStatus.className = `${badgeClass} px-2 py-0.5 text-xs font-semibold text-white rounded-full`;
        modalStatus.textContent = researcherData.status_badge;
        modalStatus.classList.remove('hidden');
        hasBadge = true;
    } else {
        modalStatus.classList.add('hidden');
    }
    
    // Set email in header
    const isValidEmail = (email) => {
        if (!email || typeof email !== 'string') return false;
        const trimmed = email.trim();
        if (trimmed.length === 0) return false;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(trimmed);
    };
    
    if (isValidEmail(researcherData.profile_link)) {
        modalEmailHeader.href = `mailto:${researcherData.profile_link.trim()}`;
        modalEmailText.textContent = researcherData.profile_link.trim();
        modalEmailHeader.classList.remove('opacity-50', 'cursor-not-allowed');
        modalEmailHeader.onclick = null;
    } else {
        modalEmailHeader.href = '#';
        modalEmailText.textContent = 'No email available';
        modalEmailHeader.classList.add('opacity-50', 'cursor-not-allowed');
        modalEmailHeader.onclick = (e) => e.preventDefault();
    }
    
    // Set biography
    if (researcherData.bio && researcherData.bio.trim()) {
        modalBio.textContent = researcherData.bio;
        modalBioSection.classList.remove('hidden');
    } else {
        modalBioSection.classList.add('hidden');
    }
    
    // Set research areas in header
    const researchAreas = Array.isArray(researcherData.research_areas) ? researcherData.research_areas : [];
    if (researchAreas.length > 0) {
        modalAreasHeader.innerHTML = researchAreas.map((area) => {
            return `<span class="text-xs text-white/90 px-2 py-0.5 bg-white/10 rounded border border-white/20">${escapeHtml(area)}</span>`;
        }).join('');
    } else {
        modalAreasHeader.innerHTML = '';
    }
    
    // Show separator if there's a badge or research areas
    if (hasBadge || researchAreas.length > 0) {
        modalNameStatusSeparator.classList.remove('hidden');
    } else {
        modalNameStatusSeparator.classList.add('hidden');
    }
    
    // Helper function to check if a link is valid
    const hasValidLink = (link) => {
        return link && typeof link === 'string' && link.trim().length > 0;
    };
    
    // Helper function to set button state
    const setButtonState = (button, isValid, link) => {
        const img = button.querySelector('img');
        if (isValid && link) {
            button.href = link;
            button.removeAttribute('disabled');
            button.onclick = null;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.style.filter = 'none';
            if (img) img.style.filter = 'none';
        } else {
            button.href = '#';
            button.setAttribute('disabled', 'disabled');
            button.onclick = (e) => e.preventDefault();
            button.classList.add('opacity-50', 'cursor-not-allowed');
            button.style.filter = 'grayscale(100%)';
            if (img) img.style.filter = 'grayscale(100%)';
        }
    };
    
    // Set link buttons - always show, disable if no valid link
    if (hasValidLink(researcherData.scopus_link)) {
        scopusBtn.target = '_blank';
        setButtonState(scopusBtn, true, researcherData.scopus_link.trim());
    } else {
        scopusBtn.removeAttribute('target');
        setButtonState(scopusBtn, false);
    }
    
    if (hasValidLink(researcherData.orcid_link)) {
        orcidBtn.target = '_blank';
        setButtonState(orcidBtn, true, researcherData.orcid_link.trim());
    } else {
        orcidBtn.removeAttribute('target');
        setButtonState(orcidBtn, false);
    }
    
    if (hasValidLink(researcherData.wos_link)) {
        wosBtn.target = '_blank';
        setButtonState(wosBtn, true, researcherData.wos_link.trim());
    } else {
        wosBtn.removeAttribute('target');
        setButtonState(wosBtn, false);
    }
    
    if (hasValidLink(researcherData.google_scholar_link)) {
        googleScholarBtn.target = '_blank';
        setButtonState(googleScholarBtn, true, researcherData.google_scholar_link.trim());
    } else {
        googleScholarBtn.removeAttribute('target');
        setButtonState(googleScholarBtn, false);
    }
    
    // Save current scroll position BEFORE any DOM changes
    const scrollY = window.scrollY || window.pageYOffset || document.documentElement.scrollTop;
    
    // Hide scroll hint if it exists
    const scrollHint = document.getElementById('scrollHint');
    if (scrollHint) {
        scrollHint.classList.remove('show');
    }
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Prevent body scroll using fixed positioning
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
    document.body.style.overflow = 'hidden';
    
    // Prevent interaction with page behind modal
    document.body.style.pointerEvents = 'none';
    // Ensure modal backdrop and content remain interactive
    modal.style.pointerEvents = 'auto';
    
    // Store scroll position for restoration
    modal.dataset.scrollY = scrollY;
};

// Close modal function
window.closeResearcherModal = function() {
    const modal = document.getElementById('researcherProfileModal');
    if (modal) {
        modal.classList.add('hidden');
        
        // Get stored scroll position
        const scrollY = parseInt(modal.dataset.scrollY || '0', 10);
        
        // Restore body styles first
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflow = '';
        
        // Restore interaction
        document.body.style.pointerEvents = '';
        modal.style.pointerEvents = '';
        
        // Restore scroll position immediately without smooth scrolling
        // Use requestAnimationFrame to ensure DOM is updated first
        requestAnimationFrame(() => {
            // Temporarily disable smooth scrolling
            const html = document.documentElement;
            const originalScrollBehavior = html.style.scrollBehavior;
            html.style.scrollBehavior = 'auto';
            
            // Restore scroll position
            window.scrollTo(0, scrollY);
            
            // Re-enable smooth scrolling after a brief delay
            requestAnimationFrame(() => {
                html.style.scrollBehavior = originalScrollBehavior;
            });
        });
        
        // Clean up
        delete modal.dataset.scrollY;
    }
};

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('researcherProfileModal');
    if (modal && e.target === modal) {
        window.closeResearcherModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.closeResearcherModal();
    }
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.landingResearchers = new LandingResearchers();
});

// Make it globally available for Turbo
window.LandingResearchers = LandingResearchers;

