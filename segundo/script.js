$(document).ready(function() {
    console.log("Document ready");

    // Pantalla de carga
    setTimeout(function() {
        $('#loading-screen').fadeOut(1000, function() {
            $('.container').addClass('loaded');
            animateLinks();
        });
    }, 3000);

    function animateLinks() {
        $('.link').each(function(index) {
            $(this).delay(200 * index).animate({opacity: 1, transform: 'translateY(0)'}, 1000);
        });
    }

    // Modal para agregar enlace
    const modal = $('#add-link-modal');
    const btn = $('#add-link-btn');
    const span = $('.close');

    btn.on('click', function() {
        console.log("Add link button clicked");
        modal.css('display', 'block');
    });

    span.on('click', function() {
        modal.css('display', 'none');
    });

    $(window).on('click', function(event) {
        if (event.target == modal[0]) {
            modal.css('display', 'none');
        }
    });

    // Formulario para agregar enlace
    $('#add-link-form').on('submit', function(e) {
        e.preventDefault();
        console.log("Form submitted");
        var formData = new FormData(this);

        $.ajax({
            url: 'add_link.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log("AJAX response:", response);
                if(response.success) {
                    alert('Enlace agregado con éxito');
                    modal.css('display', 'none');
                    location.reload(); // Recargar la página para mostrar el nuevo enlace
                } else {
                    alert('Error al agregar el enlace: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                alert('Error al procesar la solicitud');
            }
        });
    });

    // Función para obtener enlaces recientes
    function fetchRecentLinks() {
        $.ajax({
            url: 'fetch_links.php',
            method: 'GET',
            dataType: 'json',
            success: function(links) {
                let recentLinks = $('#recent-links');
                recentLinks.empty();
                links.slice(0, 50).forEach(link => {
                    recentLinks.append(`<li><a href="${link.url}">${link.title}</a></li>`);
                });
            }
        });
    }

    fetchRecentLinks();
    setInterval(fetchRecentLinks, 60000); // Actualizar cada minuto

    // Manejo de likes
    $(document).on('click', '.like-button', function() {
        let linkId = $(this).data('id');
        $.post('like_link.php', { link_id: linkId }, function(response) {
            if (response.success) {
                let likeCount = $('.link[data-id="' + linkId + '"] .like-count');
                likeCount.text(parseInt(likeCount.text()) + 1);
            }
        }, 'json');
    });

    // Manejo de comentarios
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        let form = $(this);
        let linkId = form.data('id');
        let content = form.find('textarea').val();
        $.post('comments_handler.php', { comment: true, link_id: linkId, comment_content: content }, function(response) {
            if (response.success) {
                form.find('textarea').val('');
                loadComments(linkId, true);
                updateCommentCount(linkId);
            }
        }, 'json');
    });

    function loadComments(linkId, isNewComment = false) {
        $.get('comments_handler.php', { fetch_comments: true, link_id: linkId }, function(response) {
            let commentsList = $('#comments-' + linkId);
            let loadMoreButton = $('.load-more-comments[data-id="' + linkId + '"]');
            
            if (isNewComment) {
                commentsList.empty();
            }
            
            if (response.comments.length > 3) {
                commentsList.html(response.comments.slice(0, 3).map(comment => 
                    `<p><strong>${comment.username}:</strong> ${comment.content}</p>`
                ).join(''));
                loadMoreButton.show();
            } else {
                commentsList.html(response.comments.map(comment => 
                    `<p><strong>${comment.username}:</strong> ${comment.content}</p>`
                ).join(''));
                loadMoreButton.hide();
            }
        }, 'json');
    }

    // Cargar más comentarios
    $(document).on('click', '.load-more-comments', function() {
        let linkId = $(this).data('id');
        let commentsList = $('#comments-' + linkId);
        
        $.get('comments_handler.php', { fetch_comments: true, link_id: linkId, all: true }, function(response) {
            commentsList.html(response.comments.map(comment => 
                `<p><strong>${comment.username}:</strong> ${comment.content}</p>`
            ).join(''));
            $('.load-more-comments[data-id="' + linkId + '"]').hide();
        }, 'json');
    });

    function updateCommentCount(linkId) {
        $.get('comments_handler.php', { count_comments: true, link_id: linkId }, function(response) {
            $('.link[data-id="' + linkId + '"] .comment-count').text(response.count);
        }, 'json');
    }

    // Cargar comentarios iniciales
    $('.link').each(function() {
        loadComments($(this).data('id'));
    });
});