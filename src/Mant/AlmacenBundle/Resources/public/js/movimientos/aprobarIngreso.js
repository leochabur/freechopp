$(document).ready(function(){
    $('.auting').submit(function(e){
            e.preventDefault();
            var form = $(this);
            $.post(form.attr('action'),  
                   form.serialize(), 
                   function(result){
                                    if (result.ok)
                                    {
                                        bootbox.alert({ message: "Cantidad modificada exitosamente!"});
                                    }
                   });
         

    });
    
    $('.btnsend').click(function(event){
                                        event.preventDefault();
                                        var btn = $(this);
                                        var form = 'formno';
                                        var msge = 'Cancela';
                                        if (btn.data('type') == 'ok'){
                                            msge = 'Confirma';
                                            form = 'formyes';
                                        }
                                        bootbox.confirm({
                                            message: msge+" el envio del formulario?",
                                            buttons: {
                                                confirm: {
                                                    label: 'Si',
                                                    className: 'btn-success'
                                                },
                                                cancel: {
                                                    label: 'No',
                                                    className: 'btn-danger'
                                                }
                                            },
                                            callback: function (result) {
                                                console.log(result);
                                                if (result == true){
                                                    $('.'+form).submit();
                                                }
                                            }
                                        });                                          
    });
});