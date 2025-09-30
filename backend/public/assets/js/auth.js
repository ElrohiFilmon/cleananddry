// Authentication state management
class AuthManager {
    constructor() {
        this.token = localStorage.getItem('auth_token');
        this.user = JSON.parse(localStorage.getItem('user_data') || 'null');
    }

    // Check if user is authenticated
    async checkAuthStatus() {
        try {
            const response = await fetch('/api/check-auth', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': this.token ? `Bearer ${this.token}` : ''
                },
                credentials: 'include'
            });

            if (response.ok) {
                const data = await response.json();
                if (data.authenticated && data.user) {
                    this.user = data.user;
                    localStorage.setItem('user_data', JSON.stringify(data.user));
                    this.showAuthenticatedUI();
                } else {
                    this.showGuestUI();
                }
            } else {
                this.showGuestUI();
            }
        } catch (error) {
            console.error('Auth check failed:', error);
            this.showGuestUI();
        } finally {
            this.hideLoadingScreen();
        }
    }

    // Show UI for authenticated users
    showAuthenticatedUI() {
        document.getElementById('loadingScreen').classList.add('hidden');
        document.getElementById('mainContent').classList.remove('hidden');
        document.getElementById('userMenu').classList.remove('hidden');
        document.getElementById('userActions').classList.remove('hidden');
        
        document.getElementById('authButtons').classList.add('hidden');
        document.getElementById('guestActions').classList.add('hidden');
        
        // Update user name if available
        if (this.user && this.user.name) {
            document.getElementById('userName').textContent = `Welcome, ${this.user.name}`;
        }
    }

    // Show UI for guests
    showGuestUI() {
        document.getElementById('loadingScreen').classList.add('hidden');
        document.getElementById('mainContent').classList.remove('hidden');
        document.getElementById('authButtons').classList.remove('hidden');
        document.getElementById('guestActions').classList.remove('hidden');
        
        document.getElementById('userMenu').classList.add('hidden');
        document.getElementById('userActions').classList.add('hidden');
    }

    // Hide loading screen
    hideLoadingScreen() {
        document.getElementById('loadingScreen').classList.add('hidden');
    }

    // Login function
    async login(email, password) {
        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (response.ok) {
                // Store token and user data
                this.token = data.token || data.access_token;
                this.user = data.user;
                
                localStorage.setItem('auth_token', this.token);
                localStorage.setItem('user_data', JSON.stringify(this.user));
                
                // Redirect to dashboard
                window.location.href = 'home.html';
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Login failed' };
            }
        } catch (error) {
            return { success: false, error: 'Network error occurred' };
        }
    }

    // Signup function
    async signup(userData) {
        try {
            const response = await fetch('/api/signup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                'X-CSRF-TOKEN': await this.getCsrfToken()
                },
                body: JSON.stringify(userData)
            });

            const data = await response.json();

            if (response.ok) {
                // Store token and user data
                this.token = data.token || data.access_token;
                this.user = data.user;
                
                localStorage.setItem('auth_token', this.token);
                localStorage.setItem('user_data', JSON.stringify(this.user));
                
                // Redirect to dashboard
                window.location.href = 'home.html';
                return { success: true };
            } else {
                return { 
                    success: false, 
                    error: data.message || 'Signup failed',
                    errors: data.errors 
                };
            }
        } catch (error) {
            return { success: false, error: 'Network error occurred' };
        }
    }

    // Logout function
    async logout() {
        try {
            await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': this.token ? `Bearer ${this.token}` : '',
                    'X-CSRF-TOKEN': await this.getCsrfToken()
                }
            });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            // Clear local storage
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            this.token = null;
            this.user = null;
            
            // Redirect to home page
            window.location.href = 'index.html';
        }
    }

    // Get CSRF token from meta tag
    async getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    // Check if user has specific role
    hasRole(role) {
        return this.user && this.user.role === role;
    }

    // Get current user
    getCurrentUser() {
        return this.user;
    }

    // Check if user is logged in
    isLoggedIn() {
        return !!this.user && !!this.token;
    }
}

// Global auth instance
const authManager = new AuthManager();

// Global functions for HTML onclick events
async function checkAuthStatus() {
    return await authManager.checkAuthStatus();
}

async function login(email, password) {
    return await authManager.login(email, password);
}

async function signup(userData) {
    return await authManager.signup(userData);
}

async function logout() {
    return await authManager.logout();
}

// Route protection for authenticated pages
function requireAuth() {
    if (!authManager.isLoggedIn()) {
        window.location.href = 'login.html';
        return false;
    }
    return true;
}

// Route protection for guest pages
function requireGuest() {
    if (authManager.isLoggedIn()) {
        window.location.href = 'home.html';
        return false;
    }
    return true;
}