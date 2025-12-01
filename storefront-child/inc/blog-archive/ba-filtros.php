<?php
// Shortcode: Filtro por categorías del blog
add_shortcode('filtro_blog_categorias', 'trekkium_shortcode_filtro_blog_categorias');
function trekkium_shortcode_filtro_blog_categorias() {
    // Obtener todas las categorías que tienen posts
    $categories = get_categories([
        'hide_empty' => true, // Solo categorías con posts
        'orderby' => 'name',
        'order' => 'ASC'
    ]);
    
    if (empty($categories)) return '';

    ob_start();
    ?>
    <div class="filtro-contenedor" id="filtro-categorias">
        <?php foreach ($categories as $category): ?>
            <span class="filtro-item" data-categoria="<?php echo esc_attr($category->slug); ?>">
                <?php echo esc_html($category->name); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Shortcode: Filtro por autores del blog
add_shortcode('filtro_blog_autor', 'trekkium_shortcode_filtro_blog_autor');
function trekkium_shortcode_filtro_blog_autor() {
    // Obtener autores que han publicado posts
    $authors = get_users([
        'has_published_posts' => true, // Solo autores con posts
        'orderby' => 'display_name',
        'order' => 'ASC'
    ]);
    
    if (empty($authors)) return '';

    ob_start();
    ?>
    <div class="filtro-contenedor" id="filtro-autores">
        <?php foreach ($authors as $author): ?>
            <span class="filtro-item" data-autor="<?php echo esc_attr($author->user_nicename); ?>">
                <?php echo esc_html($author->display_name); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Script para filtros del blog (versión mejorada)
add_action('wp_footer', function() {
    ?>
    <script>
    (function(){
        function normalizeTextToSlug(str){
            if(!str && str !== "") return "";
            try {
                return String(str)
                    .normalize('NFD')
                    .replace(/\p{Diacritic}/gu,'')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g,'');
            } catch(e) {
                return String(str).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g,'');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Filtros del blog
            const categoriaBtns = Array.from(document.querySelectorAll(".filtro-item[data-categoria]"));
            const autorBtns = Array.from(document.querySelectorAll(".filtro-item[data-autor]"));
            const entradas = Array.from(document.querySelectorAll(".ba-query-grid .ba-query-item"));

            if (!entradas.length) return;

            let activeCategoria = null;
            let activeAutor = null;

            // Función para obtener las opciones disponibles basadas en las entradas visibles
            function getAvailableOptions() {
                const availableCategorias = new Set();
                const availableAutores = new Set();
                
                entradas.forEach(entrada => {
                    if (entrada.style.display !== 'none') {
                        // Obtener categorías de entradas visibles
                        const entradaCategorias = entrada.getAttribute('data-categorias') || '';
                        entradaCategorias.split(',').filter(Boolean).forEach(cat => {
                            availableCategorias.add(cat);
                        });
                        
                        // Obtener autores de entradas visibles
                        const entradaAutor = entrada.getAttribute('data-autor') || '';
                        availableAutores.add(normalizeTextToSlug(entradaAutor));
                    }
                });
                
                return { categorias: availableCategorias, autores: availableAutores };
            }

            // Función para actualizar qué botones de filtro se muestran
            function updateFilterButtons() {
                const availableOptions = getAvailableOptions();
                
                // Actualizar botones de categoría
                categoriaBtns.forEach(btn => {
                    const categoriaSlug = btn.getAttribute('data-categoria');
                    
                    if ((activeAutor && !availableOptions.categorias.has(categoriaSlug)) || 
                        (!activeAutor && !availableOptions.categorias.has(categoriaSlug))) {
                        btn.classList.add('hidden');
                    } else {
                        btn.classList.remove('hidden');
                    }
                });
                
                // Actualizar botones de autor
                autorBtns.forEach(btn => {
                    const autorSlug = btn.getAttribute('data-autor');
                    
                    if ((activeCategoria && !availableOptions.autores.has(autorSlug)) || 
                        (!activeCategoria && !availableOptions.autores.has(autorSlug))) {
                        btn.classList.add('hidden');
                    } else {
                        btn.classList.remove('hidden');
                    }
                });
            }

            // Función para filtrar las entradas del blog
            function filtrarBlog(){
                entradas.forEach(entrada => {
                    const entradaCategorias = entrada.getAttribute('data-categorias') || '';
                    const entradaCategoriasArray = entradaCategorias.split(',').filter(Boolean);
                    
                    const entradaAutor = entrada.getAttribute('data-autor') || '';
                    const entradaAutorSlug = normalizeTextToSlug(entradaAutor);

                    const matchCategoria = !activeCategoria || entradaCategoriasArray.includes(activeCategoria);
                    const matchAutor = !activeAutor || entradaAutorSlug === activeAutor;

                    if (matchCategoria && matchAutor) {
                        entrada.style.display = '';
                    } else {
                        entrada.style.display = 'none';
                    }
                });
                
                updateFilterButtons();
                updateActiveFilters();
            }

            function updateActiveFilters() {
                const cont = document.querySelector('.ba-query-wrapper .filtros-activos .filtros-lista');
                if (!cont) return;
                cont.innerHTML = '';

                if (activeCategoria) {
                    const btn = categoriaBtns.find(b => (b.getAttribute('data-categoria')||'') === activeCategoria);
                    const label = btn ? btn.textContent.trim() : activeCategoria;
                    const span = document.createElement('span');
                    span.className = 'filtros-activos-item';
                    span.textContent = label + ' ';
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'quitar-filtro';
                    a.setAttribute('data-type','categoria');
                    a.setAttribute('data-value', activeCategoria);
                    a.textContent = '×';
                    span.appendChild(a);
                    cont.appendChild(span);
                }

                if (activeAutor) {
                    const btn = autorBtns.find(b => (b.getAttribute('data-autor')||'') === activeAutor);
                    const label = btn ? btn.textContent.trim() : activeAutor;
                    const span = document.createElement('span');
                    span.className = 'filtros-activos-item';
                    span.textContent = label + ' ';
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'quitar-filtro';
                    a.setAttribute('data-type','autor');
                    a.setAttribute('data-value', activeAutor);
                    a.textContent = '×';
                    span.appendChild(a);
                    cont.appendChild(span);
                }
            }

            // Delegación para quitar filtros desde las pestañas
            document.addEventListener('click', function(e){
                const target = e.target;
                if (target && target.classList && target.classList.contains('quitar-filtro')) {
                    e.preventDefault();
                    const type = target.getAttribute('data-type');
                    const value = target.getAttribute('data-value');
                    if (!type) return;

                    if (type === 'categoria') {
                        categoriaBtns.forEach(b => {
                            if ((b.getAttribute('data-categoria')||'') === value) b.classList.remove('active');
                        });
                        activeCategoria = null;
                    } else if (type === 'autor') {
                        autorBtns.forEach(b => {
                            if ((b.getAttribute('data-autor')||'') === value) b.classList.remove('active');
                        });
                        activeAutor = null;
                    }

                    filtrarBlog();
                }
            });

            // Configurar los eventos click para los botones de filtro
            function setupBlogButtons(buttons, type) {
                buttons.forEach(btn => {
                    btn.addEventListener('click', function(){
                        const key = type === 'categoria' ? 'data-categoria' : 'data-autor';
                        const value = this.getAttribute(key);
                        const isActive = this.classList.contains('active');
                        
                        if (isActive) {
                            // Desactivar el filtro
                            this.classList.remove('active');
                            if (type === 'categoria') {
                                activeCategoria = null;
                            } else {
                                activeAutor = null;
                            }
                        } else {
                            // Activar el filtro
                            if (type === 'categoria') {
                                // Desactivar otros botones de categoría
                                categoriaBtns.forEach(b => b.classList.remove('active'));
                                activeCategoria = value;
                            } else {
                                // Desactivar otros botones de autor
                                autorBtns.forEach(b => b.classList.remove('active'));
                                activeAutor = value;
                            }
                            this.classList.add('active');
                        }
                        
                        filtrarBlog();
                    });
                });
            }

            if (categoriaBtns.length) setupBlogButtons(categoriaBtns, 'categoria');
            if (autorBtns.length) setupBlogButtons(autorBtns, 'autor');
            
            // Inicializar ocultando opciones que no tienen entradas
            updateFilterButtons();
        });
    })();
    </script>
    <?php
});