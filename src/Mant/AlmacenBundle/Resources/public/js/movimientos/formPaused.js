$(document).ready(function(){

  $(".sing").on("click", function(event) {
                                            event.preventDefault();
                                            var btn = $(this);
                                            var form = btn.parent();
                                            bootbox.prompt({
                                                title: "Ingrese su clave...",
                                                inputType: 'password',
                                                size: 'small',
                                                callback: function (result) {
                                                    if (result)
                                                    {
                                                        $.post(form.attr('action'), {data:result}, function(data){
                                                                                                                        $('.btn-load').click();
                                                                                                                 });
                                                        
                                                    }
                                                }
                                            });                                            
  });
  
  $(".view").on("click", function(event) {
                                            event.preventDefault();
                                            var a = $(this);
                                            $('.modal-body').load(a.attr('href'),function(){
                                                    $('#myModal').modal({show:true});
                                            });
  });  
    
    
    
});