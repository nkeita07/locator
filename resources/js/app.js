document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("articleSearchForm");
    if (!form) return;

    const input = document.getElementById("article_search_input");

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        let reference = input.value.trim();
        if (reference.length < 1) return;

        // 🔥 Redirection vers Laravel pour charger la page emplacement
        window.location.href = `/article/emplacement/${reference}`;
    });
});
