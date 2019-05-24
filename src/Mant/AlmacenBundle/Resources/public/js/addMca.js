$(document).ready(function(){
    $('.btn-add-mca').click(function(e){
            e.preventDefault();
            var form = $('#add-mca');
            $.post(form.attr('action'),
                   form.serialize(),
                   function(event){
                                    console.log(event);
                                    if (event.state){
                                        location.reload();
                                    }
                                    else{
                                        alert(event.msge);
                                    }
                   });

    });
});