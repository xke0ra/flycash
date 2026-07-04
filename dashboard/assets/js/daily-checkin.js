function dailyCheckin(){
    
    var title = "Daily Checkin";
    var description = "You'll get this reward daily. So, please login daily to get this reward.";
    var button = "OK";
    
    if (typeof daily_checkin_title !== 'undefined') {
        title = daily_checkin_title;
    }
    
    if (typeof daily_checkin_title !== 'undefined') {
        description = daily_checkin_description;
    }
    
    if (typeof daily_checkin_title !== 'undefined') {
        button = daily_checkin_button_text;
    }
    
    swal.fire({
          title: title,
          text: description,
          showCancelButton: true,
          icon: 'question',
          confirmButtonText: button,
          showLoaderOnConfirm: true,
          preConfirm: () => {
            
            var api = '../admin/controller/daily-checkin.php';
                var formData = new FormData();
                formData.append('userId', "userId");
                  
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
        }).then((result) => {
            
            if (result.value) {
                
                if(result.value.error_code == "100"){
                    
                    swal.fire({
                          title: "Success",
                          text: result.value.error_description,
                          showCancelButton: false,
                          allowOutsideClick: false,
                          icon: 'success',
                          confirmButtonText: 'OK',
                          preConfirm: () => {
                              
                              location.reload(true);
                          }
                     })
                      
		          }else if(result.value.error_code == "420"){
		              
		              swal.fire("oops!", result.value.error_description, "error");
		              
		          }else{
		              
		              swal.fire("Server Problem", "please try after some time", "error");
		              
		          }
		          
              }
              
        })
    
}

var timestamp = 10;
if (typeof dailycheckintime !== 'undefined') {
    
    timestamp = dailycheckintime;
}

function component(x, v) {
    return Math.floor(x / v);
}

var $div = $('daily-checkin-timer');

setInterval(function() {
    
    timestamp--;
    
    if(timestamp >= 0){
        
        $('.daily-checkin-taken-title').show();
        $('.daily-checkin-try-again').show();

        var days    = component(timestamp, 24 * 60 * 60),
        hours   = component(timestamp,      60 * 60) % 24,
        minutes = component(timestamp,           60) % 60,
        seconds = component(timestamp,            1) % 60;
        
        $div.html(hours + ":" + minutes + ":" + seconds);
        //$div.html(days + " days, " + hours + ":" + minutes + ":" + seconds);
    
    }else{
        
        $('.daily-checkin-taken-title').hide();
        $('.daily-checkin-try-again').hide();
        
        $div.html('<button onclick="dailyCheckin()" class="btn btn-light">Checkin Today</button>');
    }
    
}, 1000);