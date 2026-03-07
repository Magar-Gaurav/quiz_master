// document.addEventListener("DOMContentLoaded", function () {
//     AOS.init({
//         duration: 1000,
//          once: false
//     });
// });

const questions = document.querySelectorAll(".faq-question");
questions.forEach(q => {
    q.addEventListener("click", () => {
        const answer = q.nextElementSibling;
        const arrow = q.querySelector(".arrow");

        const isOpen = answer.style.maxHeight;

        // Close all
        document.querySelectorAll(".faq-answer").forEach(a => a.style.maxHeight = null);
        document.querySelectorAll(".arrow").forEach(ar => ar.style.transform = "rotate(0deg)");

        // Toggle current
        if (!isOpen) {
            answer.style.maxHeight = answer.scrollHeight + "px";
            arrow.style.transform = "rotate(180deg)";
        }
    });
});
