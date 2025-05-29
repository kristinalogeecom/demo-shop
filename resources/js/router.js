class Router {
    constructor() {
        this.routes = {};
        this.initEventListeners();
        this.navigateTo(location.hash.slice(1) || 'dashboard');

    }

    addRoute(route, callback) {
        this.routes[route] = callback;
    }

    navigateTo(route) {
        if (this.routes[route]) {
            history.pushState({}, '', `#${route}`);

            // Update active menu item
            document.querySelectorAll('[data-route]').forEach(link => {
                link.classList.toggle('active', link.getAttribute('data-route') === route);
            });

            this.routes[route]();
        }
    }

    initEventListeners() {
        // Handle menu clicks
        document.querySelectorAll('[data-route]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.navigateTo(e.target.getAttribute('data-route'));
            });
        });

        // Handle browser back/forward
        window.addEventListener('popstate', () => {
            this.navigateTo(location.hash.slice(1));
        });
    }
}

export const router = new Router();