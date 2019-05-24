$(document).ready(function(){
    $('.search').keyup(function (e) {
                          var searchTerm = $(".search").val();
                          var listItem = $('.stmm tbody').children('tr .finder');
                          var searchSplit = searchTerm.replace(/ /g, "'):containsi('");
                          
                          $.extend($.expr[':'], {'containsi': function(elem, i, match, array){
                                return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
                            }
                          });
                            
                          $(".finder").not(":containsi('" + searchSplit + "')").each(function(e){
                            $(this).hide();
                          });
                        
                          $(".finder:containsi('" + searchSplit + "')").each(function(e){
                            $(this).show();
                          });
    });
    
    $('.form-upd').submit(function(e){
            e.preventDefault();
            var form = $(this);
            $.post(form.attr('action'),
                   form.serialize(),
                   function(event){
                                bootbox.alert({
                                    message: event.msge,
                                    size: 'large'
                                });                              
                   });

    });
});