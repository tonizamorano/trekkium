(function() {
    // Guard contra ejecución múltiple
    if (window.AvatarEditorLoaded) {
        console.log('AvatarEditor ya fue cargado, evitando duplicación');
        return;
    }
    window.AvatarEditorLoaded = true;

    /**
     * AvatarEditor - Gestiona upload y eliminación de avatares
     * Usa jQuery AJAX para compatibilidad con WordPress
     */
    class AvatarEditor {
        constructor(options = {}) {
            this.ajaxUrl = options.ajaxUrl;
            this.uploadNonce = options.uploadNonce;
            this.deleteNonce = options.deleteNonce;

            if (!this.ajaxUrl) {
                console.error('AvatarEditor: ajaxUrl no está definida');
                return;
            }

            this.init();
        }

        init() {
            this.attachEventListeners();
        }

        attachEventListeners() {
            const self = this;
            const changeBtn = document.getElementById('avatar-change-btn');
            const deleteBtn = document.getElementById('avatar-delete-btn');
            const fileInput = document.getElementById('avatar-file-input');

            if (changeBtn) {
                changeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    fileInput?.click();
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        self.validateAndUpload(file);
                    }
                    fileInput.value = '';
                });
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (confirm('¿Seguro que quieres eliminar tu avatar?')) {
                        self.deleteAvatar();
                    }
                });
            }
        }

        validateAndUpload(file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!validTypes.includes(file.type)) {
                alert('Por favor, selecciona solo archivos de imagen (JPEG, PNG, GIF).');
                return;
            }

            if (file.size > maxSize) {
                alert('La imagen no puede ser mayor a 2MB.');
                return;
            }

            this.uploadAvatar(file);
        }

        uploadAvatar(file) {
            const self = this;
            const formData = new FormData();
            formData.append('action', 'subir_avatar_usuario');
            formData.append('avatar_file', file);
            formData.append('security', this.uploadNonce);

            jQuery.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        document.getElementById('user-avatar-preview').src = response.data.url;
                        alert('Avatar subido correctamente');
                    } else {
                        const message = response.data?.message || 'Error desconocido';
                        alert('Error: ' + message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error al subir la imagen: ' + textStatus);
                }
            });
        }

        deleteAvatar() {
            const self = this;

            jQuery.post(this.ajaxUrl, {
                action: 'eliminar_avatar_usuario',
                security: this.deleteNonce
            }, function(response) {
                if (response.success) {
                    document.getElementById('user-avatar-preview').src = response.data.url;
                    alert('Avatar eliminado correctamente');
                } else {
                    alert('Error: ' + (response.data?.message || 'Error al eliminar el avatar'));
                }
            }, 'json');
        }
    }

    // Inicializar si estamos en la página de editar avatar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAvatarEditor);
    } else {
        initAvatarEditor();
    }

    function initAvatarEditor() {
        const avatarEditor = document.getElementById('avatar-editor');
        if (!avatarEditor) return;

        if (typeof my_avatar_ajax === 'undefined' || !my_avatar_ajax.ajax_url) {
            console.error('La variable my_avatar_ajax no está definida');
            return;
        }

        new AvatarEditor({
            ajaxUrl: my_avatar_ajax.ajax_url,
            uploadNonce: my_avatar_ajax.upload_nonce || '',
            deleteNonce: my_avatar_ajax.delete_nonce || ''
        });
    }

    window.AvatarEditor = AvatarEditor;
})();
