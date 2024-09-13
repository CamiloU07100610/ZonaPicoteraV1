document.addEventListener('DOMContentLoaded', function() {
    var uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);

            // Display success message immediately
            document.getElementById('uploadMessage').innerHTML = `
                <div class="alert alert-success"><a href="../index.php">Ver mi publicación</a></div>`;

            xhr.onload = function() {
                console.log('Upload complete:', xhr.status, xhr.responseText); // Debug log
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Additional actions if needed
                    } else {
                        document.getElementById('uploadMessage').innerHTML = `
                            <div class="alert alert-danger">Error al subir el archivo.</div>`;
                    }
                } else {
                    document.getElementById('uploadMessage').innerHTML = `
                        <div class="alert alert-danger">Error al subir el archivo.</div>`;
                }
            };

            xhr.send(formData);
        });
    }

        var deleteForms = document.querySelectorAll('form.d-inline');
        deleteForms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            form.closest('li').remove();
                        } else {
                            alert('Error al eliminar el contenido.');
                        }
                    } else {
                        alert('Error al eliminar el contenido.');
                    }
                };

                xhr.send(formData);
            });
        });
});