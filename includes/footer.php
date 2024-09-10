</main>
<footer class="bg-light text-center text-lg-start mt-4">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Acerca de nosotros</h5>
                <p>
                    Somos una plataforma dedicada a compartir contenido multimedia de alta calidad.
                </p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">Enlaces</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="acerca.php" class="text-dark">Acerca de</a></li>
                    <li><a href="contacto.php" class="text-dark">Contacto</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-0">Síguenos</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-dark"><i class="fab fa-facebook"></i> Facebook</a></li>
                    <li><a href="#" class="text-dark"><i class="fab fa-twitter"></i> Twitter</a></li>
                    <li><a href="#" class="text-dark"><i class="fab fa-instagram"></i> Instagram</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © 2024 Copyright:
        <a class="text-dark" href="#">TuEmpresa.com</a>
    </div>
</footer>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var videoLinks = document.querySelectorAll('.video-link');
        var modalVideo = document.getElementById('modalVideo');
        var videoModalLabel = document.getElementById('videoModalLabel');
        var commentsList = document.getElementById('commentsList');
        var commentForm = document.getElementById('commentForm');

        videoLinks.forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                var videoSrc = link.getAttribute('data-video-src');
                var videoTitle = link.getAttribute('data-video-title');
                var videoId = link.getAttribute('data-video-id');
                modalVideo.src = videoSrc;
                videoModalLabel.textContent = videoTitle;
                $('#videoModal').modal('show');

                // Load comments
                fetch('obtener_comentarios.php?id=' + videoId)
                    .then(response => response.json())
                    .then(data => {
                        commentsList.innerHTML = '';
                        data.forEach(comment => {
                            var commentDiv = document.createElement('div');
                            commentDiv.classList.add('card', 'mb-2');
                            commentDiv.innerHTML = `
                                <div class="card-body">
                                    <p class="card-text">${comment.comentario}</p>
                                    <footer class="blockquote-footer">${comment.usuario_id}</footer>
                                </div>
                            `;
                            commentsList.appendChild(commentDiv);
                        });
                    });

                // Handle comment form submission
                if (commentForm) {
                    commentForm.onsubmit = function(e) {
                        e.preventDefault();
                        var formData = new FormData(commentForm);
                        formData.append('contenido_id', videoId);

                        fetch('agregar_comentario.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    var newCommentDiv = document.createElement('div');
                                    newCommentDiv.classList.add('card', 'mb-2');
                                    newCommentDiv.innerHTML = `
                                    <div class="card-body">
                                        <p class="card-text">${formData.get('comentario')}</p>
                                        <footer class="blockquote-footer">${data.usuario_id}</footer>
                                    </div>
                                `;
                                    commentsList.appendChild(newCommentDiv);
                                    commentForm.reset();
                                } else {
                                    alert('Error al agregar el comentario.');
                                }
                            });
                    };
                }
            });
        });

        $('#videoModal').on('hidden.bs.modal', function() {
            modalVideo.pause();
            modalVideo.src = '';
            commentsList.innerHTML = '';
        });
    });
</script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
