/*
*   Template : Pocket - Money Making Script
*   Author: AYM
*   Website: http://www.aym.com/
*   Contact: support@aym.com
*   Purchase from CodyHub : http://codyhub.com/
*   Purchase from Codecanyon : http://codecanyon.net/
*   Support: http://AYM.com/support
*   License: You must have a valid license purchased only from codyhub or codecanyon (the above links) in order to legally use this product.
*
*/
		var KTAppOptions = {
			"colors": {
				"state": {
					"brand": "#366cf3",
					"light": "#ffffff",
					"dark": "#282a3c",
					"primary": "#5867dd",
					"success": "#34bfa3",
					"info": "#36a3f7",
					"warning": "#ffb822",
					"danger": "#fd3995"
				},
				"base": {
					"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
					"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
				}
			}
		};
			
		
		function copyReferCodeToClipboard(value) {
		    var tempInput = document.createElement("input");
		    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
		    tempInput.value = value;
		    document.body.appendChild(tempInput);
		    tempInput.select();
		    document.execCommand("copy");
		    document.body.removeChild(tempInput);
		    
		    swal.fire({
                title: "Great !",
                text: "Referral Code Copied to clipboard.",
                type: "success",
                buttonsStyling: 1,
                confirmButtonText: "<i class='la la-thumbs-up'></i> Ok, Thanks",
                confirmButtonClass: "btn btn-info"
            })
            
		   }
		    
		function copyReferURLToClipboard(value) {
		    var tempInput = document.createElement("input");
		    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
		    tempInput.value = value;
		    document.body.appendChild(tempInput);
		    tempInput.select();
		    document.execCommand("copy");
		    document.body.removeChild(tempInput);
		    
		    swal.fire({
                title: "Great !",
                text: "Referral URL Copied to clipboard.",
                type: "success",
                buttonsStyling: 1,
                confirmButtonText: "<i class='la la-thumbs-up'></i> Ok, Thanks",
                confirmButtonClass: "btn btn-info"
            })
            
		}
			
		function showNoEnoughPointsAlert() {
		    swal.fire({
                title: "No Enough Points!",
                text: "You do not have enough points to make a Redeem.",
                type: "warning",
                buttonsStyling: 1,
                confirmButtonText: "Ok",
                confirmButtonClass: "btn btn-info"
            })
		}
		
		function showRedeemAlert(orderId, title, subTitle, placeHolder){
		    
		    swal.fire({
              title: title,
              text: subTitle,
              input: 'text',
              inputPlaceholder: placeHolder,
              inputAttributes: {
                autocapitalize: 'off'
              },
              showCancelButton: true,
              inputValidator: (value) => {
                if (!value) {
                  return 'You need to write something!'
                }
              },
              confirmButtonText: 'Proceed',
              showLoaderOnConfirm: true,
              preConfirm: (inputValue) => {
                  
                var api = '../admin/controller/redeem-processor.php';
                var formData = new FormData();
                formData.append('orderId', orderId);
                formData.append('pos', inputValue);
                  
                return fetch(api,{
                    method: 'POST',
                    body: formData
                  })
                  .then(response => {
                    if (!response.ok) {
                      throw new Error(response.statusText)
                    }
                    return response.json()
                  })
                  .catch(error => {
                    swal.showValidationMessage(
                      `Request failed: ${error}`
                    )
                  })
              },
              allowOutsideClick: () => !swal.isLoading()
            })
            .then((result) => {
              if (result.value) {
                  
                  if(result.value.error_code == "100"){
                      
                      swal.fire("Success !", "Great, we have received your withdrawl request. \n Track your order Status in Transactions Page", "success");
                      
		          }else if(result.value.error_code == "108"){
		              
		              swal.fire("Something Problem", "Contact Developer immediately !", "error");
		              
		          }else if(result.value.error_code == "911"){
		              
		              swal.fire("Sorry !", "your Points were Debited but our server did not saved your Redeem Request. So, please Contact us on this issue immediately !", "warning");
		              
		          }else if(result.value.error_code == "210"){
		              
		              swal.fire("No Enough Points!", "You do not have enough points to make a Redeem.", "warning");
		              
		          }else if(result.value.error_code == "420"){
		              
		              swal.fire("Malicious Activity Found", "your account will be blocked if we found any suspicious activity in your logs ", "warning");
		              
		          }else{
		              
		              swal.fire("Server Problem", "please try after some time", "error");
		              
		          }
		          
              }
            })

		}