// Fonction pour envoyer un commentaire
function postComment() {
    var commentText = document.getElementById("comment-input").value;
    
    if (commentText === "") {
        alert("Veuillez écrire un commentaire!");
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "comment.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Ajout du commentaire sans recharger la page
            var commentData = JSON.parse(xhr.responseText);
            var commentList = document.getElementById("comments-list");
            
            var newComment = document.createElement("div");
            newComment.classList.add("comment");
            newComment.innerHTML = "<p>" + commentData.comment + "</p><span>Posté à " + commentData.timestamp + "</span>";
            commentList.appendChild(newComment);
            
            document.getElementById("comment-input").value = ""; // Effacer le champ de texte
        }
    };
    
    xhr.send("comment=" + encodeURIComponent(commentText));
}
