// Clean&Dry SPA Authentication Layer

class AuthManager {
    // Always reads from localStorage
    get token() {
        // Support both new and legacy keys for backward compatibility
        return localStorage.getItem('auth_token') || localStorage.getItem('token');
    }
    get user() {
        try {
            // Support both new and legacy keys for backward compatibility
            const user = localStorage.getItem('user_data') || localStorage.getItem('user');
            return user ? JSON.parse(user) : null;
        } catch {
            return null;
        }
    }

    // Is user logged in?
    isLoggedIn() {
        return !!this.token && !!this.user;
    }

    // Get current user
    getCurrentUser() {
        return this.user;
    }

    // Login and store user/token
    async login(email, password) {
        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await response.json();

            if (response.ok && data.token && data.user) {
                // Remove legacy keys if present
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                // Store with new keys
                localStorage.setItem('auth_token', data.token);
                localStorage.setItem('user_data', JSON.stringify(data.user));
                window.location.href = 'home.html';
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Login failed' };
            }
        } catch (error) {
            return { success: false, error: 'Network error occurred' };
        }
    }

    // Signup and store user/token
    async signup(userData) {
        try {
            const response = await fetch('/api/signup', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });
            const data = await response.json();

            if (response.ok && data.token && data.user) {
                // Remove legacy keys if present
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                // Store with new keys
                localStorage.setItem('auth_token', data.token);
                localStorage.setItem('user_data', JSON.stringify(data.user));
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

    // Logout, clear storage, redirect
    async logout() {
        try {
            await fetch('/api/logout', {
                method: 'POST',
                headers: { 'Authorization': this.token ? `Bearer ${this.token}` : '' }
            });
        } catch (_) { /* Ignore network/logout errors */ }
        // Remove both new and legacy keys
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_data');
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = 'login.html';
    }

    // Route protection for authenticated pages
    requireAuth() {
        if (!this.isLoggedIn()) {
            window.location.href = 'login.html';
            return false;
        }
        return true;
    }

    // Route protection for guest pages
    requireGuest() {
        if (this.isLoggedIn()) {
            window.location.href = 'home.html';
            return false;
        }
        return true;
    }
}

// Global auth instance
const authManager = new AuthManager();

// For HTML onclick events if needed
async function login(email, password) { return await authManager.login(email, password); }
async function signup(userData) { return await authManager.signup(userData); }
async function logout() { return await authManager.logout(); }
function requireAuth() { return authManager.requireAuth(); }
function requireGuest() { return authManager.requireGuest(); }