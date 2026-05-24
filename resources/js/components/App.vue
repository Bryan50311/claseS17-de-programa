<template>
  <div class="app-container">
    <!-- Encabezado / Header -->
    <header class="app-header">
      <div class="logo-area">
        <span class="icon-book">📖</span>
        <div class="title-desc">
          <h1>Librería Nacional</h1>
          <p>Sistema de Control de Inventario</p>
        </div>
      </div>
      <button class="btn btn-primary btn-add" @click="openModal">
        <span class="btn-icon">✚</span> Registrar Nuevo Libro
      </button>
    </header>

    <!-- Alertas de Éxito / Notificaciones -->
    <transition name="slide-fade">
      <div v-if="successMessage" class="alert alert-success">
        <span class="alert-icon">✓</span>
        <div class="alert-content">
          <strong>¡Completado!</strong>
          <p>{{ successMessage }}</p>
        </div>
        <button class="alert-close" @click="successMessage = ''">&times;</button>
      </div>
    </transition>

    <!-- Barra de Filtros -->
    <section class="filters-section">
      <span class="filter-label">Filtrar por Categoría:</span>
      <div class="filter-buttons">
        <button 
          class="btn-filter" 
          :class="{ active: selectedCategoryFilter === 'all' }"
          @click="selectedCategoryFilter = 'all'"
        >
          Todas
        </button>
        <button 
          v-for="cat in categories" 
          :key="cat.id" 
          class="btn-filter"
          :class="{ active: selectedCategoryFilter === cat.id }"
          @click="selectedCategoryFilter = cat.id"
        >
          {{ cat.nombre }}
        </button>
      </div>
    </section>

    <!-- Listado de Libros -->
    <main class="books-display">
      <div v-if="loadingBooks" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando catálogo de libros...</p>
      </div>

      <div v-else-if="filteredBooks.length === 0" class="empty-state">
        <span class="empty-icon">📚</span>
        <h3>No hay libros registrados</h3>
        <p v-if="selectedCategoryFilter === 'all'">Comienza agregando un nuevo libro al inventario mediante el botón superior.</p>
        <p v-else>No se encontraron libros para la categoría seleccionada.</p>
      </div>

      <div v-else class="books-grid">
        <div v-for="book in filteredBooks" :key="book.id" class="book-card">
          <!-- Portada -->
          <div class="book-cover-wrapper">
            <img 
              v-if="book.imagen_portada" 
              :src="'/storage/' + book.imagen_portada" 
              :alt="book.nombre"
              class="book-cover"
              @error="handleImageError"
            />
            <div v-else class="book-cover-placeholder">
              <span>📖</span>
              <p>Sin Portada</p>
            </div>
            <!-- Etiqueta de Categoría -->
            <span class="category-badge" :title="book.category?.descripcion || 'Sin descripción'">
              {{ book.category?.nombre || 'General' }}
            </span>
          </div>

          <!-- Información del Libro -->
          <div class="book-info">
            <h3 class="book-title">{{ book.nombre }}</h3>
            <p class="book-author">Por <strong>{{ book.autor }}</strong></p>
            
            <div class="book-metadata">
              <div class="meta-item">
                <span class="meta-label">Editorial:</span>
                <span class="meta-value">{{ book.editorial }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Edición:</span>
                <span class="meta-value">{{ book.edicion }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Lanzamiento:</span>
                <span class="meta-value">{{ formatDate(book.fecha_lanzamiento) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Formulario de Registro (Estilo Glassmorphism Premium) -->
    <transition name="fade">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-card">
          <div class="modal-header">
            <h2>Registrar Nuevo Libro</h2>
            <button class="modal-close-btn" @click="closeModal">&times;</button>
          </div>

          <form @submit.prevent="submitBookForm" class="modal-body" enctype="multipart/form-data">
            <!-- Título -->
            <div class="form-group">
              <label for="nombre">Nombre / Título del Libro <span class="required">*</span></label>
              <input 
                type="text" 
                id="nombre" 
                v-model="form.nombre" 
                class="form-control" 
                :class="{ 'has-error': errors.nombre }"
                placeholder="Ej. Cien Años de Soledad"
              />
              <span v-if="errors.nombre" class="error-msg">{{ errors.nombre[0] }}</span>
            </div>

            <!-- Autor y Editorial -->
            <div class="form-row">
              <div class="form-group col">
                <label for="autor">Autor <span class="required">*</span></label>
                <input 
                  type="text" 
                  id="autor" 
                  v-model="form.autor" 
                  class="form-control" 
                  :class="{ 'has-error': errors.autor }"
                  placeholder="Ej. Gabriel García Márquez"
                />
                <span v-if="errors.autor" class="error-msg">{{ errors.autor[0] }}</span>
              </div>
              <div class="form-group col">
                <label for="editorial">Editorial <span class="required">*</span></label>
                <input 
                  type="text" 
                  id="editorial" 
                  v-model="form.editorial" 
                  class="form-control" 
                  :class="{ 'has-error': errors.editorial }"
                  placeholder="Ej. Editorial Sudamericana"
                />
                <span v-if="errors.editorial" class="error-msg">{{ errors.editorial[0] }}</span>
              </div>
            </div>

            <!-- Edición y Fecha de Lanzamiento -->
            <div class="form-row">
              <div class="form-group col">
                <label for="edicion">Edición <span class="required">*</span></label>
                <input 
                  type="text" 
                  id="edicion" 
                  v-model="form.edicion" 
                  class="form-control" 
                  :class="{ 'has-error': errors.edicion }"
                  placeholder="Ej. Primera Edición"
                />
                <span v-if="errors.edicion" class="error-msg">{{ errors.edicion[0] }}</span>
              </div>
              <div class="form-group col">
                <label for="fecha_lanzamiento">Fecha de Lanzamiento <span class="required">*</span></label>
                <input 
                  type="date" 
                  id="fecha_lanzamiento" 
                  v-model="form.fecha_lanzamiento" 
                  class="form-control" 
                  :class="{ 'has-error': errors.fecha_lanzamiento }"
                />
                <span v-if="errors.fecha_lanzamiento" class="error-msg">{{ errors.fecha_lanzamiento[0] }}</span>
              </div>
            </div>

            <!-- Categoría -->
            <div class="form-group">
              <label for="categoria_id">Categoría <span class="required">*</span></label>
              <select 
                id="categoria_id" 
                v-model="form.categoria_id" 
                class="form-control"
                :class="{ 'has-error': errors.categoria_id }"
              >
                <option value="">-- Selecciona una categoría --</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.nombre }}
                </option>
              </select>
              <span v-if="errors.categoria_id" class="error-msg">{{ errors.categoria_id[0] }}</span>
              <p class="field-help" v-if="selectedCategoryDesc">
                <strong>Descripción:</strong> {{ selectedCategoryDesc }}
              </p>
            </div>

            <!-- Portada de Imagen con Vista Previa -->
            <div class="form-group">
              <label>Imagen de Portada <span class="required">*</span></label>
              <div class="image-upload-wrapper" :class="{ 'has-error': errors.imagen_portada }">
                <!-- Dropzone/Selector interactivo -->
                <label for="imagen_portada" class="image-upload-label">
                  <div v-if="!imagePreview" class="upload-prompt">
                    <span class="upload-icon">📷</span>
                    <p class="upload-title">Seleccionar imagen</p>
                    <p class="upload-subtitle">Formatos permitidos: JPG, PNG o WEBP (máx. 2MB)</p>
                  </div>
                  <div v-else class="upload-preview-container">
                    <img :src="imagePreview" alt="Vista previa de portada" class="upload-preview" />
                    <div class="upload-overlay">
                      <span>Cambiar Imagen</span>
                    </div>
                  </div>
                </label>
                <input 
                  type="file" 
                  id="imagen_portada" 
                  class="file-input-hidden" 
                  accept="image/*"
                  @change="onFileChange"
                />
              </div>
              <span v-if="errors.imagen_portada" class="error-msg">{{ errors.imagen_portada[0] }}</span>
            </div>

            <!-- Botones de Acción -->
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal" :disabled="submitting">
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary" :disabled="submitting">
                <span v-if="submitting" class="spinner-small"></span>
                {{ submitting ? 'Registrando...' : 'Guardar Libro' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'App',
  data() {
    return {
      books: [],
      categories: [],
      selectedCategoryFilter: 'all',
      showModal: false,
      loadingBooks: true,
      submitting: false,
      successMessage: '',
      imagePreview: null,
      errors: {},
      form: {
        nombre: '',
        autor: '',
        editorial: '',
        edicion: '',
        fecha_lanzamiento: '',
        categoria_id: '',
        imagen_portada: null
      }
    };
  },
  computed: {
    // Filtrar libros según la categoría seleccionada
    filteredBooks() {
      if (this.selectedCategoryFilter === 'all') {
        return this.books;
      }
      return this.books.filter(book => book.categoria_id === this.selectedCategoryFilter);
    },
    // Obtener la descripción de la categoría seleccionada en el formulario
    selectedCategoryDesc() {
      if (!this.form.categoria_id) return '';
      const cat = this.categories.find(c => c.id === parseInt(this.form.categoria_id));
      return cat ? cat.descripcion : '';
    }
  },
  mounted() {
    this.fetchCategories();
    this.fetchBooks();
  },
  methods: {
    // Obtener categorías desde la base de datos
    async fetchCategories() {
      try {
        const response = await axios.get('/api/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Error al obtener categorías:', error);
      }
    },
    // Obtener libros con su categoría asociada
    async fetchBooks() {
      this.loadingBooks = true;
      try {
        const response = await axios.get('/api/books');
        this.books = response.data;
      } catch (error) {
        console.error('Error al obtener libros:', error);
      } finally {
        this.loadingBooks = false;
      }
    },
    // Controlar el cambio de archivo para la vista previa de la portada
    onFileChange(e) {
      const file = e.target.files[0];
      if (!file) return;

      this.form.imagen_portada = file;

      // Crear URL temporal para previsualizar la imagen en el formulario
      const reader = new FileReader();
      reader.onload = (event) => {
        this.imagePreview = event.target.result;
      };
      reader.readAsDataURL(file);
    },
    // Abrir modal y reiniciar formulario
    openModal() {
      this.resetForm();
      this.showModal = true;
    },
    // Cerrar modal
    closeModal() {
      if (this.submitting) return;
      this.showModal = false;
      this.resetForm();
    },
    // Reiniciar datos del formulario
    resetForm() {
      this.form = {
        nombre: '',
        autor: '',
        editorial: '',
        edicion: '',
        fecha_lanzamiento: '',
        categoria_id: '',
        imagen_portada: null
      };
      this.imagePreview = null;
      this.errors = {};
    },
    // Enviar el formulario a la API
    async submitBookForm() {
      this.submitting = true;
      this.errors = {};

      const formData = new FormData();
      formData.append('nombre', this.form.nombre);
      formData.append('autor', this.form.autor);
      formData.append('editorial', this.form.editorial);
      formData.append('edicion', this.form.edicion);
      formData.append('fecha_lanzamiento', this.form.fecha_lanzamiento);
      formData.append('categoria_id', this.form.categoria_id);
      
      if (this.form.imagen_portada) {
        formData.append('imagen_portada', this.form.imagen_portada);
      }

      try {
        const response = await axios.post('/api/books', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        if (response.data.success) {
          // Agregar el nuevo libro al inicio de la lista
          this.books.unshift(response.data.book);
          this.successMessage = response.data.message;
          this.closeModal();

          // Auto-ocultar la notificación después de 4 segundos
          setTimeout(() => {
            this.successMessage = '';
          }, 4000);
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          // Guardar errores de validación de Laravel
          this.errors = error.response.data.errors;
        } else {
          console.error('Error al registrar libro:', error);
          alert('Hubo un error inesperado al registrar el libro. Por favor intenta de nuevo.');
        }
      } finally {
        this.submitting = false;
      }
    },
    // Manejar fallos de carga de imagen usando un placeholder genérico
    handleImageError(e) {
      e.target.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="150" height="200" viewBox="0 0 150 200"><rect width="100%" height="100%" fill="%23f0f3f5"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Outfit, sans-serif" font-size="14" fill="%23a0aec0">Imagen no disponible</text></svg>';
    },
    // Formatear fechas del backend a un formato legible "DD/MM/AAAA"
    formatDate(dateStr) {
      if (!dateStr) return '';
      const parts = dateStr.split('-');
      if (parts.length !== 3) return dateStr;
      return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
  }
};
</script>
