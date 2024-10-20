document.addEventListener('DOMContentLoaded', function() {

    function handleFormSubmit(event) {
        event.preventDefault(); 
        
        url = new URL(window.location.href);
        split_path = url.pathname.split('/');
        id = parseInt(split_path[split_path.length - 1], 10);

        if (!id) {
            return;
        }

        form = event.target;
        urlEnd = '';
        method = '';

        if (form.id === 'put_rating_form') {
            urlEnd = 'rate';
            method = 'PUT';
        }
        else if (form.id === 'delete_rating_form') {
            urlEnd = 'rate';
            method = 'DELETE';
        }
        else if (form.id === 'put_review_form') {
            urlEnd = 'review';
            method = 'PUT';
        }
        else if (form.id === 'delete_review_form') {
            urlEnd = 'review';
            method = 'DELETE';
        }
        
        finalUrl = `/catalogue/${id}/${urlEnd}`;
        refreshUrl = `/catalogue/${id}`;

        formData = new URLSearchParams(new FormData(form));

        fetch(finalUrl, {
            method: method,
            body: formData 
        }) 
        .then(data => {
            return fetch(refreshUrl, {
                method: 'GET'
            });
        }) 
        .then(response => {
            return response.text();
        })
        .then(html => {
            document.body.innerHTML = html;
            attachEventListeners();
        });

    }

    function attachEventListeners() {
        try {
            document.getElementById('put_rating_form').addEventListener('submit', handleFormSubmit);
        }
        catch(error) {}
        try {
            document.getElementById('delete_rating_form').addEventListener('submit', handleFormSubmit);
        } 
        catch (error) {}

        try {
            document.getElementById('put_review_form').addEventListener('submit', handleFormSubmit);
        } 
        catch (error) {}

        try {
            document.getElementById('delete_review_form').addEventListener('submit', handleFormSubmit);
        } 
        catch (error) {}
    }

    attachEventListeners();
});
