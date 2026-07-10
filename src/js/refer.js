/**
 * Referral clipboard functions
 */

function showSwal(title, text, type) {
  if (typeof swal !== 'undefined' && swal.fire) {
    swal.fire({
      title,
      text,
      type,
      buttonsStyling: 1,
      confirmButtonText: "<i class='la la-thumbs-up'></i> Ok, Thanks",
      confirmButtonClass: "btn btn-info"
    });
  }
}

export function copyReferCodeToClipboard(value) {
  const tempInput = document.createElement("input");
  tempInput.style = "position: absolute; left: -1000px; top: -1000px";
  tempInput.value = value;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);

  showSwal("Great !", "Referral Code Copied to clipboard.", "success");
}

export function copyReferURLToClipboard(value) {
  const tempInput = document.createElement("input");
  tempInput.style = "position: absolute; left: -1000px; top: -1000px";
  tempInput.value = value;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);

  showSwal("Great !", "Referral URL Copied to clipboard.", "success");
}

// Expose globally for inline onclick handlers
window.copyReferCodeToClipboard = copyReferCodeToClipboard;
window.copyReferURLToClipboard = copyReferURLToClipboard;
