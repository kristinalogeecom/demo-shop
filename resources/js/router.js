class Router {
    constructor() {
        this.routes = {};
        this.initEventListeners();

        setTimeout(() => {
            this.navigateTo(location.hash.slice(1) || 'dashboard');
        }, 0);
    }

    addRoute(route, callback) {
        this.routes[route] = callback;
    }

    async navigateTo(route) {
        if (this.routes[route]) {
            history.pushState({}, '', `#${route}`);

            this.updateActiveRoute(route);

            try {
                await this.routes[route]();
            } catch (error) {
                console.error(`Error while navigating to "${route}":`, error);
                const app = document.getElementById('app');
                if (app) {
                    app.innerHTML = `
                        <div class="error">
                            <h2>Routing Error</h2>
                            <p>${error.message}</p>
                        </div>
                    `;
                }
            }
        } else {
            console.warn(`No route registered for "${route}"`);
        }
    }

    updateActiveRoute(activeRoute) {
        document.querySelectorAll('[data-route]').forEach(link => {
            link.classList.toggle('active', link.getAttribute('data-route') === activeRoute);
        });
    }

    initEventListeners() {
        document.querySelectorAll('[data-route]').forEach(link => {
            link.addEventListener('click', async (e) => {
                e.preventDefault();
                const route = e.target.closest('[data-route]')?.getAttribute('data-route');
                if (route) {
                    await this.navigateTo(route);
                }
            });
        });

        window.addEventListener('popstate', () => {
            const route = location.hash.slice(1);
            this.navigateTo(route);
        });
    }
}

export const router = new Router();
