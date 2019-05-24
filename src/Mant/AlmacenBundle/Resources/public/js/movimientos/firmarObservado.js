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

  $(".obs").on("click", function(event) {
                                            event.preventDefault();
                                            var btn = $(this);
                                            bootbox.prompt({
                                                title: "Observacion: "+btn.data("tipo")+" "+btn.data("nro"),
                                                inputType: 'textarea',
                                                value: observaciones[btn.data('id')],
                                                placeholder: 'Ingrese la observacion...',                                                
                                                size: 'large',
                                                callback: function (result) {
                                                    if (result)
                                                    {
                                                        $.get(btn.attr('href'), {data:result}, function(ret){
                                                                                                                $('.btn-load').click();
                                                                                                            });
                                                    }
                                                }
                                            });    
  });    
    
    
    
});