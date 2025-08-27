// ✅ Afficher / masquer le formulaire
function toggleForm() {
  const formBox = document.getElementById("formBox");
  formBox.style.display =
    formBox.style.display === "none" || formBox.style.display === ""
      ? "block"
      : "none";
}

// ✅ Confirmation avant suppression
document.addEventListener("DOMContentLoaded", () => {
  const deleteBtns = document.querySelectorAll(".delete-btn");
  deleteBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      if (!confirm("Voulez-vous vraiment supprimer cet enregistrement ?")) {
        e.preventDefault(); // Annule l'action
      }
    });
  });
});

var modal = document.getElementById('ibtn');

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "block";
  }
}