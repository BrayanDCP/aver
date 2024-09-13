// category-ajax.js

class CategoryManager {
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
        this.categories = [];
        this.lastSearchQuery = '';
        this.searchDebounceTimer = null;
    }

    // Método para realizar solicitudes AJAX con caché y reintentos
    async ajaxRequest(action, data, retries = 3) {
        try {
            const response = await fetch(this.baseUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ action, ...data })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Unknown error occurred');
            }

            return result;
        } catch (error) {
            if (retries > 0) {
                await new Promise(resolve => setTimeout(resolve, 1000));
                return this.ajaxRequest(action, data, retries - 1);
            }
            throw error;
        }
    }

    // Método mejorado para mostrar mensajes con tipos adicionales
    showMessage(type, message, duration = 3000) {
        const messageElement = document.getElementById('message');
        messageElement.textContent = message;
        messageElement.className = `message ${type} show`;
        setTimeout(() => {
            messageElement.className = `message ${type}`;
        }, duration);
    }

    // Método para eliminar una categoría con confirmación personalizada
    async deleteCategory(id) {
        const category = this.categories.find(c => c.id === id);
        if (!category) {
            this.showMessage('error', 'Categoría no encontrada');
            return;
        }

        const result = await this.showConfirmDialog(`¿Estás seguro de que quieres eliminar la categoría "${category.nombre_categoria}"?`);
        if (result) {
            try {
                await this.ajaxRequest('delete', { id });
                this.categories = this.categories.filter(c => c.id !== id);
                this.updateCategoryGrid();
                this.showMessage('success', 'Categoría eliminada con éxito');
            } catch (error) {
                this.showMessage('error', `Error al eliminar la categoría: ${error.message}`);
            }
        }
    }

    // Método para añadir una categoría con validación mejorada
    async addCategory() {
        const nameInput = document.getElementById('categoryName');
        const name = nameInput.value.trim();
        if (name.length < 3) {
            this.showMessage('error', 'El nombre de la categoría debe tener al menos 3 caracteres');
            return;
        }

        try {
            const data = await this.ajaxRequest('add', { name });
            this.categories.push(data.category);
            this.updateCategoryGrid();
            nameInput.value = '';
            this.showMessage('success', 'Categoría añadida con éxito');
        } catch (error) {
            this.showMessage('error', `Error al añadir la categoría: ${error.message}`);
        }
    }

    // Método para editar una categoría con validación
    async editCategory(id) {
        const category = this.categories.find(c => c.id === id);
        if (!category) {
            this.showMessage('error', 'Categoría no encontrada');
            return;
        }

        const newName = await this.showPromptDialog('Editar nombre de categoría', category.nombre_categoria);
        if (newName && newName.trim().length >= 3) {
            try {
                const data = await this.ajaxRequest('edit', { id, name: newName.trim() });
                category.nombre_categoria = data.category.nombre_categoria;
                this.updateCategoryGrid();
                this.showMessage('success', 'Categoría actualizada con éxito');
            } catch (error) {
                this.showMessage('error', `Error al actualizar la categoría: ${error.message}`);
            }
        } else if (newName) {
            this.showMessage('error', 'El nombre de la categoría debe tener al menos 3 caracteres');
        }
    }

    // Método para cargar todas las categorías
    async loadCategories() {
        try {
            const data = await this.ajaxRequest('getAll', {});
            this.categories = data.categories;
            this.updateCategoryGrid();
        } catch (error) {
            this.showMessage('error', `Error al cargar las categorías: ${error.message}`);
        }
    }

    // Método para actualizar la cuadrícula de categorías
    updateCategoryGrid() {
        const categoryGrid = document.querySelector('.category-grid');
        categoryGrid.innerHTML = '';
        const fragment = document.createDocumentFragment();

        this.categories
            .filter(category => category.nombre_categoria.toLowerCase().includes(this.lastSearchQuery.toLowerCase()))
            .forEach(category => {
                const categoryCard = this.createCategoryCard(category);
                fragment.appendChild(categoryCard);
            });

        categoryGrid.appendChild(fragment);
        this.animateCategoryChanges();
    }

    // Método para crear una tarjeta de categoría
    createCategoryCard(category) {
        const card = document.createElement('div');
        card.className = 'category-card';
        card.dataset.id = category.id;
        card.innerHTML = `
            <div class="category-name">${this.highlightSearch(category.nombre_categoria)}</div>
            <div class="btn-group">
                <button class="btn btn-edit">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn btn-delete">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        `;

        card.querySelector('.btn-edit').addEventListener('click', () => this.editCategory(category.id));
        card.querySelector('.btn-delete').addEventListener('click', () => this.deleteCategory(category.id));

        return card;
    }

    // Método para resaltar el texto de búsqueda
    highlightSearch(text) {
        if (!this.lastSearchQuery) return text;
        const regex = new RegExp(`(${this.lastSearchQuery})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    // Método para animar los cambios en las categorías
    animateCategoryChanges() {
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });
    }

    // Método mejorado para buscar categorías con debounce
    searchCategories(query) {
        clearTimeout(this.searchDebounceTimer);
        this.searchDebounceTimer = setTimeout(() => {
            this.lastSearchQuery = query;
            this.updateCategoryGrid();
        }, 300);
    }

    // Método para mostrar un diálogo de confirmación personalizado
    showConfirmDialog(message) {
        return new Promise(resolve => {
            const dialog = document.createElement('div');
            dialog.className = 'custom-dialog';
            dialog.innerHTML = `
                <div class="dialog-content">
                    <p>${message}</p>
                    <div class="dialog-buttons">
                        <button class="btn btn-cancel">Cancelar</button>
                        <button class="btn btn-confirm">Confirmar</button>
                    </div>
                </div>
            `;

            dialog.querySelector('.btn-cancel').addEventListener('click', () => {
                document.body.removeChild(dialog);
                resolve(false);
            });

            dialog.querySelector('.btn-confirm').addEventListener('click', () => {
                document.body.removeChild(dialog);
                resolve(true);
            });

            document.body.appendChild(dialog);
        });
    }

    // Método para mostrar un diálogo de entrada personalizado
    showPromptDialog(title, defaultValue = '') {
        return new Promise(resolve => {
            const dialog = document.createElement('div');
            dialog.className = 'custom-dialog';
            dialog.innerHTML = `
                <div class="dialog-content">
                    <h3>${title}</h3>
                    <input type="text" class="dialog-input" value="${defaultValue}">
                    <div class="dialog-buttons">
                        <button class="btn btn-cancel">Cancelar</button>
                        <button class="btn btn-confirm">Confirmar</button>
                    </div>
                </div>
            `;

            const input = dialog.querySelector('.dialog-input');

            dialog.querySelector('.btn-cancel').addEventListener('click', () => {
                document.body.removeChild(dialog);
                resolve(null);
            });

            dialog.querySelector('.btn-confirm').addEventListener('click', () => {
                const value = input.value.trim();
                document.body.removeChild(dialog);
                resolve(value);
            });

            document.body.appendChild(dialog);
            input.focus();
            input.select();
        });
    }

    // Método para inicializar los event listeners
    initEventListeners() {
        document.getElementById('categoryName').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.addCategory();
            }
        });

        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.searchCategories(e.target.value);
        });

        document.getElementById('addCategoryBtn').addEventListener('click', () => this.addCategory());
    }

    // Método para inicializar el gestor de categorías
    async init() {
        try {
            await this.loadCategories();
            this.initEventListeners();
            this.showMessage('info', 'Sistema de categorías inicializado', 2000);
        } catch (error) {
            this.showMessage('error', `Error al inicializar: ${error.message}`);
        }
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    window.categoryManager = new CategoryManager(window.location.href);
    categoryManager.init();
});