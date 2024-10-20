function listForums() {

    fetch('/api/forums')
        .then(response => response.json())
        .then(data => {
            const forumList = document.getElementById('forum-list');
            data.forEach(forum => {
                const listItem = document.createElement('li');
                const link = document.createElement('a');
                link.href = '/forums/' + forum.id + '/posts';
                link.textContent = forum.title;
                listItem.appendChild(link);
                forumList.appendChild(listItem);
            });
        })
        .catch(error => console.error('Error fetching forums:', error));
}

function handleCreateForum(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const formDataObject = {};
    formData.forEach((value, key) => {
        formDataObject[key] = value;
    });
    const jsonData = JSON.stringify(formDataObject);

    fetch('/api/forums', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: jsonData
    })
        .then(() => {
            location.reload();
        })
        .catch(error => console.error('Error creating forum:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    listForums();
    document.getElementById('create-forum-form').addEventListener('submit', handleCreateForum);
});