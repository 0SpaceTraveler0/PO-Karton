async function postData(url = '') {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).catch (function (error) {
        console.log('Request failed', error);
    });
    return await response.json(); // parses JSON response into native JavaScript objects
}

function funonload(url,dateFrom,dateTo) {
    postData(url)
        .then((data) => {
            createTableBody(data);
        })
}


