function listPosts() {

    const url = window.location.pathname;
    const urlSplit = url.split('/');
    const id = parseInt(urlSplit[urlSplit.length - 2], 10);

    fetch('/api/forums/' + id)
        .then(response => response.json())
        .then(data => {
            const forumTitle = document.getElementById('title');
            const forumDescription = document.getElementById('description');
            forumTitle.textContent = data.title;
            forumDescription.textContent = data.description;
        })
        .catch(error => console.error('Error fetching forums:', error));

    fetch('/api/forums/' + id + '/posts')
        .then(response => response.json())
        .then(data => {
            const postList = document.getElementById('post-list');
            data.forEach(post => {
                const postItem = document.createElement('div');

                const userImage = document.createElement('img');
                const userName = document.createElement('span');
                const postTitle = document.createElement('div');
                const postContents = document.createElement('div');

                if (post.opProfilePicture === "") {
                    userImage.src = "/assets/images/default.jpg";
                }
                else {
                    userImage.src = post.opProfilePicture;
                }

                userImage.classList.add('profile-image');
                userName.textContent = post.opUsername;
                postTitle.textContent = post.title;
                postContents.textContent = post.contents;

                postItem.appendChild(userImage);
                postItem.appendChild(userName);
                postItem.appendChild(document.createElement('br'));
                postItem.appendChild(document.createElement('br'));
                postItem.appendChild(postTitle);
                postItem.appendChild(postContents);
                postItem.appendChild(document.createElement('br'));

                postList.appendChild(postItem);
            });
        })
        .catch(error => console.error('Error fetching forums:', error));
}

function handleCreatePost(event) {
    event.preventDefault();

    const url = window.location.pathname;
    const urlSplit = url.split('/');
    const id = parseInt(urlSplit[urlSplit.length - 2], 10);

    const formData = new FormData(event.target);
    const formDataObject = {};
    formData.forEach((value, key) => {
        if (key === "forumId") value = id;
        if (key === "userId") value = parseInt(value);
        formDataObject[key] = value;
    });
    const jsonData = JSON.stringify(formDataObject);

    fetch('/api/forums/' + id + '/posts', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: jsonData
    })
        .then(() => {
            location.reload();
        })
}

document.addEventListener('DOMContentLoaded', function() {
    listPosts();
    document.getElementById('create-post-form').addEventListener('submit', handleCreatePost);
});