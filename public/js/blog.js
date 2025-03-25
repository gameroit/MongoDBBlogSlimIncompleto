document.addEventListener('DOMContentLoaded', function() {
    loadPosts();
    loadAuthors();

    // Manejar formulario de nuevo post
    document.getElementById('newPostForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const post = {
            title: formData.get('title'),
            body: formData.get('body'),
            author: formData.get('author'),
            tags: formData.get('tags').split(',').map(tag => tag.trim()),
            permalink: generatePermalink()
        };

        fetch('/api/posts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(post)
        })
        .then(response => response.json())
        .then(() => {
            bootstrap.Modal.getInstance(document.getElementById('newPostModal')).hide();
            loadPosts();
        });
    });

    // Manejar formulario de nuevo comentario
    document.getElementById('newCommentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const postId = formData.get('postId');
        const comment = {
            body: formData.get('body'),
            author: formData.get('author'),
            email: formData.get('email')
        };

        fetch(`/api/posts/${postId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(comment)
        })
        .then(response => response.json())
        .then(() => {
            bootstrap.Modal.getInstance(document.getElementById('newCommentModal')).hide();
            loadPost(postId);
        });
    });
});

function loadPosts(tag = null, author = null, commentAuthor = null, skip = 0) {
    let url = '/api/posts';
    let params = [];
    let currentFilters = {tag, author, commentAuthor};
    
    // Filtrar solo los parámetros que tienen valor
    Object.entries(currentFilters).forEach(([key, value]) => {
        if (value && value !== 'null' && value !== 'undefined') {
            params.push(`${key}=${encodeURIComponent(value)}`);
        }
    });
    
    params.push(`skip=${skip}`);
    url += '?' + params.join('&');

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('posts-container');
            if (!data.data || !data.data.length) {
                container.innerHTML = '<div class="alert alert-info">No se encontraron posts</div>';
                return;
            }
            
            let html = data.data.map(post => createPostHtml(post)).join('');
            
            // Crear objetos para los parámetros de navegación
            const navParams = {
                tag: currentFilters.tag || null,
                author: currentFilters.author || null,
                commentAuthor: currentFilters.commentAuthor || null
            };
            
            // Añadir botones de navegación
            html += `
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-secondary ${skip === 0 ? 'disabled' : ''}" 
                            onclick="loadPosts(${JSON.stringify(navParams.tag)}, ${JSON.stringify(navParams.author)}, ${JSON.stringify(navParams.commentAuthor)}, ${Math.max(0, skip - 5)})">
                        Anterior
                    </button>
                    <button class="btn btn-secondary ${skip + 5 >= data.total ? 'disabled' : ''}"
                            onclick="loadPosts(${JSON.stringify(navParams.tag)}, ${JSON.stringify(navParams.author)}, ${JSON.stringify(navParams.commentAuthor)}, ${skip + 5})">
                        Siguiente
                    </button>
                </div>`;
            
            container.innerHTML = html;
        });
}

function loadPost(id) {
    fetch(`/api/posts/${id}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('posts-container');
            container.innerHTML = createPostHtml(data.data, true);
        });
}

function createPostHtml(post, showFullPost = false) {
    const comments = post.comments.map(comment => `
        <div class="border-start border-2 ps-3 mb-2">
            <p class="mb-1"><small>${comment.body}</small></p>
            <small class="text-muted">Por: 
                <a href="#" onclick="loadPosts(null, null, '${comment.author}')" class="text-decoration-none">
                    ${comment.author}
                </a>
            </small>
        </div>
    `).join('');

    return `
        <div class="card mb-2">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title mb-1">${post.title}</h5>
                    <small class="text-muted">Por: 
                        <a href="#" onclick="loadPosts(null, '${post.author}')" class="text-decoration-none">
                            ${post.author}
                        </a>
                    </small>
                </div>
                <p class="card-text small mb-2">
                    ${showFullPost ? post.body : post.body.substring(0, 150) + (post.body.length > 150 ? '...' : '')}
                </p>
                <div class="mb-2">
                    ${post.tags.map(tag => `
                        <a href="#" onclick="loadPosts('${tag}')" class="badge bg-secondary text-decoration-none me-1">${tag}</a>
                    `).join('')}
                </div>
                ${showFullPost ? `
                    <div class="border-top pt-2">
                        <h6 class="mb-2">Comentarios</h6>
                        ${comments}
                        <button class="btn btn-sm btn-primary mt-2" onclick="openCommentModal('${post._id}')">
                            Añadir Comentario
                        </button>
                    </div>
                ` : `
                    <button class="btn btn-link btn-sm p-0" onclick="loadPost('${post._id}')">Leer más</button>
                `}
            </div>
        </div>
    `;
}

function openCommentModal(postId) {
    document.getElementById('commentPostId').value = postId;
    new bootstrap.Modal(document.getElementById('newCommentModal')).show();
}

function generatePermalink() {
    return Math.random().toString(36).substring(2, 15);
}

function loadAuthors() {
    fetch('/api/comments/authors')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('authors-container');
            if (!data.data || !data.data.length) {
                container.innerHTML = '<p class="text-muted">No hay autores de comentarios disponibles</p>';
                return;
            }

            const authorList = data.data
                .map(author => `
                    <a href="#" 
                       onclick="loadPosts(null, null, '${author}')" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        ${author}
                        <span class="badge bg-primary rounded-pill">Comentarista</span>
                    </a>
                `)
                .join('');

            container.innerHTML = `
                <div class="list-group">
                    ${authorList}
                </div>`;
        });
}
