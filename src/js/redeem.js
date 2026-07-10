/**
 * Redeem alert functions
 */
import { addCsrfToFormData } from './csrf.js';

export function showNoEnoughPointsAlert() {
  if (typeof swal !== 'undefined' && swal.fire) {
    swal.fire({
      title: "No Enough Points!",
      text: "You do not have enough points to make a Redeem.",
      type: "warning",
      buttonsStyling: 1,
      confirmButtonText: "Ok",
      confirmButtonClass: "btn btn-info"
    });
  }
}

export function showRedeemAlert(orderId, title, subTitle, placeHolder) {
  if (typeof swal === 'undefined' || !swal.fire) return;

  swal.fire({
    title: title,
    text: subTitle,
    input: 'text',
    inputPlaceholder: placeHolder,
    inputAttributes: { autocapitalize: 'off' },
    showCancelButton: true,
    inputValidator: (value) => {
      if (!value) return 'You need to write something!';
    },
    confirmButtonText: 'Proceed',
    showLoaderOnConfirm: true,
    preConfirm: (inputValue) => {
      const api = '../admin/controller/redeem-processor.php';
      const formData = new FormData();
      formData.append('orderId', orderId);
      formData.append('pos', inputValue);
      addCsrfToFormData(formData);

      return fetch(api, { method: 'POST', body: formData })
        .then(response => {
          if (!response.ok) throw new Error(response.statusText);
          return response.json();
        })
        .catch(error => {
          swal.showValidationMessage(`Request failed: ${error}`);
        });
    },
    allowOutsideClick: () => !swal.isLoading()
  })
  .then((result) => {
    if (!result.value) return;

    const code = result.value.error_code;
    const messages = {
      "100": { title: "Success !", text: "Great, we have received your withdrawl request. \n Track your order Status in Transactions Page", type: "success" },
      "108": { title: "Something Problem", text: "Contact Developer immediately !", type: "error" },
      "911": { title: "Sorry !", text: "your Points were Debited but our server did not saved your Redeem Request. So, please Contact us on this issue immediately !", type: "warning" },
      "210": { title: "No Enough Points!", text: "You do not have enough points to make a Redeem.", type: "warning" },
      "420": { title: "Malicious Activity Found", text: "your account will be blocked if we found any suspicious activity in your logs ", type: "warning" }
    };

    const msg = messages[code] || { title: "Server Problem", text: "please try after some time", type: "error" };
    swal.fire(msg.title, msg.text, msg.type);
  });
}

// Expose globally for inline onclick handlers
window.showNoEnoughPointsAlert = showNoEnoughPointsAlert;
window.showRedeemAlert = showRedeemAlert;
