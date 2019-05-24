$(document).ready(function(){

    $('#search').keyup(function (e) {
                          var searchTerm = $(".search").val();
                          console.log(searchTerm);
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
    
    $('.btnupdate').click(function(e){
            e.preventDefault();
            var row = $(this).parents('tr');
            var id = row.data('id');
            var form = $('#form_update');
            var smin = $('#si'+id).val();
            $('#form_valor').val(smin);         
            var url = form.attr('action').replace(':ART_ID', id);
            var data = form.serialize();
            bootbox.confirm({
                message: "Seguro modificar el stock del articulo?",
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
                    if (result == true){
                                    $.post(url, data, function(result){
                                                                        alert(result);       
                                                                      });
                    }
                }
            });            

    });
});