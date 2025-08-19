// Gestion du panier
class Cart {
    constructor() {
        this.init();
    }

    init() {
        this.updateCartCount();
        this.bindEvents();
    }

    bindEvents() {
        // Boutons "Ajouter au panier"
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart')) {
                e.preventDefault();
                const productId = e.target.dataset.id;
                this.addToCart(productId);
            }
        });

        // Boutons de quantité dans le panier
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quantity-btn')) {
                const productId = e.target.dataset.id;
                const action = e.target.classList.contains('plus') ? 'plus' : 'minus';
                this.updateQuantity(productId, action);
            }
        });

        // Input de quantité
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const productId = e.target.dataset.id;
                const quantity = parseInt(e.target.value);
                this.setQuantity(productId, quantity);
            }
        });

        // Boutons supprimer
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item')) {
                const productId = e.target.dataset.id;
                this.removeFromCart(productId);
            }
        });
    }

    async addToCart(productId) {
        try {
            const response = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId,
                    quantity: 1
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount(data.cart_count);
                this.showNotification('Produit ajouté au panier !', 'success');
                
                // Mettre à jour le bouton
                const button = document.querySelector(`[data-id="${productId}"].add-to-cart`);
                if (button) {
                    button.textContent = 'Ajouté !';
                    button.disabled = true;
                    setTimeout(() => {
                        button.textContent = 'Ajouter au panier';
                        button.disabled = false;
                    }, 2000);
                }
            }
        } catch (error) {
            console.error('Erreur lors de l\'ajout au panier:', error);
            this.showNotification('Erreur lors de l\'ajout au panier', 'error');
        }
    }

    async updateQuantity(productId, action) {
        const currentQuantity = parseInt(document.querySelector(`[data-id="${productId}"].quantity-input`).value);
        let newQuantity = currentQuantity;
        
        if (action === 'plus') {
            newQuantity = currentQuantity + 1;
        } else if (action === 'minus') {
            newQuantity = Math.max(0, currentQuantity - 1);
        }

        await this.setQuantity(productId, newQuantity);
    }

    async setQuantity(productId, quantity) {
        try {
            const response = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update',
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount(data.cart_count);
                
                // Si la quantité est 0, supprimer l'élément du DOM
                if (quantity <= 0) {
                    const cartItem = document.querySelector(`[data-id="${productId}"]`);
                    if (cartItem) {
                        cartItem.remove();
                    }
                }
                
                // Mettre à jour le total
                this.updateCartTotal();
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour de la quantité:', error);
        }
    }

    async removeFromCart(productId) {
        try {
            const response = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'remove',
                    product_id: productId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount(data.cart_count);
                
                // Supprimer l'élément du DOM
                const cartItem = document.querySelector(`[data-id="${productId}"]`);
                if (cartItem) {
                    cartItem.remove();
                }
                
                // Mettre à jour le total
                this.updateCartTotal();
                
                this.showNotification('Produit retiré du panier', 'success');
            }
        } catch (error) {
            console.error('Erreur lors de la suppression:', error);
        }
    }

    async updateCartCount(count = null) {
        const cartCountElement = document.querySelector('.cart-count');
        if (!cartCountElement) return;

        if (count !== null) {
            cartCountElement.textContent = count;
        } else {
            // Récupérer le nombre d'articles depuis l'API
            try {
                const response = await fetch('/api/cart.php');
                const data = await response.json();
                cartCountElement.textContent = data.cart_count;
            } catch (error) {
                console.error('Erreur lors de la mise à jour du panier:', error);
            }
        }
    }

    updateCartTotal() {
        // Calculer le total localement
        let total = 0;
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(item => {
            const price = parseFloat(item.querySelector('.item-price').textContent.replace('€', '').trim());
            const quantity = parseInt(item.querySelector('.quantity-input').value);
            total += price * quantity;
        });

        // Mettre à jour l'affichage du total
        const totalElement = document.querySelector('.cart-summary .total span:last-child');
        if (totalElement) {
            totalElement.textContent = total.toFixed(2) + ' €';
        }
    }

    showNotification(message, type = 'info') {
        // Créer la notification
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Styles de base
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        `;

        // Couleurs selon le type
        if (type === 'success') {
            notification.style.background = '#34c759';
        } else if (type === 'error') {
            notification.style.background = '#ff3b30';
        } else {
            notification.style.background = '#0071e3';
        }

        // Ajouter au DOM
        document.body.appendChild(notification);

        // Animer l'entrée
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}

// Gestion des formulaires
class FormManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Validation des formulaires
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('form')) {
                this.validateForm(e.target, e);
            }
        });

        // Validation en temps réel
        document.addEventListener('input', (e) => {
            if (e.target.closest('.form')) {
                this.validateField(e.target);
            }
        });
    }

    validateForm(form, event) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault();
            this.showFormError('Veuillez corriger les erreurs dans le formulaire');
        }
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Supprimer les anciens messages d'erreur
        this.removeFieldError(field);

        // Validation selon le type
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Ce champ est requis';
        } else if (field.type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Email invalide';
        } else if (field.type === 'password' && value && value.length < 6) {
            isValid = false;
            errorMessage = 'Le mot de passe doit contenir au moins 6 caractères';
        }

        if (!isValid) {
            this.showFieldError(field, errorMessage);
        }

        return isValid;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    showFieldError(field, message) {
        field.classList.add('error');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            color: #ff3b30;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        `;
        
        field.parentNode.appendChild(errorDiv);
    }

    removeFieldError(field) {
        field.classList.remove('error');
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    showFormError(message) {
        // Supprimer les anciens messages
        const existingError = document.querySelector('.form-error');
        if (existingError) {
            existingError.remove();
        }

        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error error';
        errorDiv.textContent = message;
        
        const form = document.querySelector('.form');
        if (form) {
            form.insertBefore(errorDiv, form.firstChild);
        }
    }
}

// Gestion de la recherche
class SearchManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        const searchInput = document.querySelector('.search-form input[type="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(() => {
                this.handleSearch(searchInput.value);
            }, 300));
        }
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    async handleSearch(query) {
        if (query.length < 2) return;

        try {
            const response = await fetch(`/search.php?q=${encodeURIComponent(query)}`);
            const html = await response.text();
            
            // Mettre à jour les résultats si on est sur la page de recherche
            const resultsContainer = document.querySelector('.search-results');
            if (resultsContainer) {
                // Extraire le contenu des résultats depuis la réponse
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newResults = doc.querySelector('.search-results');
                
                if (newResults) {
                    resultsContainer.innerHTML = newResults.innerHTML;
                }
            }
        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
        }
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    new Cart();
    new FormManager();
    new SearchManager();
    
    // Animation des éléments au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observer les cartes de produits et catégories
    document.querySelectorAll('.product-card, .category-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});