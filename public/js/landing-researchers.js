class LandingResearchers {
    constructor() {
        this.apiUrl = '/api/researchers';
        this.container = document.getElementById('researchers-container');
        this.loadingElement = document.getElementById('researchers-loading');
        this.emptyElement = document.getElementById('researchers-empty');
        this.errorElement = document.getElementById('researchers-error');
        
        if (!this.container) {
            console.warn('Researchers container not found');
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
            console.error('Error loading researchers:', error);
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
        card.className = 'flex-shrink-0 w-64 bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 hover:-translate-y-2 overflow-hidden researcher-card';
        card.setAttribute('data-name', researcher.name || '');
        card.setAttribute('data-tags', (researcher.research_areas || []).join(' '));
        
        // Get background color classes
        const bgClasses = this.getBackgroundClasses(researcher.background_color || 'maroon');
        const statusBadgeClasses = this.getStatusBadgeClasses(researcher.status_badge || 'Active');
        
        // Create research areas HTML
        const researchAreasHtml = this.createResearchAreasHtml(researcher.research_areas || []);
        
        // Create profile link
        const profileLink = researcher.profile_link || '#';
        const linkTarget = profileLink.startsWith('http') ? '_blank' : '_self';
        
        // Create photo HTML
        const photoHtml = researcher.photo_path 
            ? `<img src="/storage/${researcher.photo_path}" alt="${this.escapeHtml(researcher.name || '')}" class="w-20 h-20 rounded-full object-cover">`
            : `<div class="w-20 h-20 ${bgClasses.avatar} rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 ${bgClasses.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 0 18 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>`;
        
        card.innerHTML = `
            <div class="relative">
                <div class="h-48 ${bgClasses.background} flex items-center justify-center">
                    ${photoHtml}
                </div>
                <div class="absolute top-4 right-4">
                    <span class="${statusBadgeClasses} text-white text-xs px-3 py-1 rounded-full font-medium shadow-lg">${this.escapeHtml(researcher.status_badge || 'Active')}</span>
                </div>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">${this.escapeHtml(researcher.name || '')}</h3>
                    <p class="text-sm text-gray-600 mb-3">${this.escapeHtml(researcher.title || '')}</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        ${researchAreasHtml}
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">${this.escapeHtml(researcher.bio || '')}</p>
                </div>
                <a href="${this.escapeHtml(profileLink)}" target="${linkTarget}" class="inline-flex items-center justify-center w-full px-4 py-2 mt-4 text-sm font-semibold rounded-lg bg-maroon-600 text-white hover:bg-maroon-700 transition">View Profile</a>
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
            return `<span class="${colorClass} text-xs px-2 py-1 rounded-full">${this.escapeHtml(area)}</span>`;
        }).join('');
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.landingResearchers = new LandingResearchers();
});

// Make it globally available for Turbo
window.LandingResearchers = LandingResearchers;
