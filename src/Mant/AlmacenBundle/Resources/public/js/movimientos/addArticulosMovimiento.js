$(document).ready(function() {
        $(".search").keyup(function (e) {
                                  var searchTerm = $(".search").val();
                                  var listItem = $('.results tbody').children('tr');
                                  var searchSplit = searchTerm.replace(/ /g, "'):containsi('");
                                  
                                  $.extend($.expr[':'], {'containsi': function(elem, i, match, array){
                                        return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
                                    }
                                  });
                                    
                                  $(".results tbody tr").not(":containsi('" + searchSplit + "')").each(function(e){
                                    $(this).attr('visible','false');
                                  });
                                
                                  $(".results tbody tr:containsi('" + searchSplit + "')").each(function(e){
                                    $(this).attr('visible','true');
                                  });
                                
                                  var jobCount = $('.results tbody tr[visible="true"]').length;
                                    $('.counter').text(jobCount + ' Articulos');
                                
                                  if(jobCount == '0') {$('.no-result').show();}
                                  else {
                                    $('.no-result').hide();
                                    if(e.which == 13) {
                                      if (jobCount == 1){
                                        $('.results tbody tr[visible="true"]').trigger( "click" );
                                        $(".search").val('');
                                        $(".search").keyup(); 
                                        $('.cant-item').focus();                                        
                                      }
                                    }
                                  }
		    });
		  
	$('.unitprice').on('focus', function(){$(this).select();});
  $(".trtable").click(function(){
                                  var articulo = $(this);
                                  $('#form_articulo').val(articulo.data('id'));
                                  articulo.children().each(function(index){
                                                                            if (index < 2){ 
                                                                              $(".rowdata").eq(index).children().first().val($(this).html());
                                                                            }
                                                                            else if (index == 4){
                                                                              $(".rowdata").eq(4).children().first().val($(this).html());
                                                                            }
                                                                            
                                  });
                                  $('.cant-item').focus();
                                  
  });
       
  $("#form_add :submit").click(function(event){
                                           
                                              event.preventDefault();
                                              var form = $('#form_add');
                                              var url_old = form.attr('action');
                                              var url_new = form.attr('action').replace(':ID_ART', $('#form_articulo').val());
                                              var data = form.serialize();
                                              $(this).hide();
                                              $.post(url_new,
                                                     data,
                                                     function(response){
                                                                        if (response.ok)
                                                                        {
                                                                          var items = response.items;
                                                                          $(".assign tbody").empty();
                                                                          items.forEach( function(valor, indice) {
                                                                                                                  
                                                                                                                 $(".assign tbody").append(generateRow(valor));
                                                                                                                    
                                                                          });
                                                                          $(".rowvalue").val('');
                                                                          if (response.warning){
                                                                            bootbox.alert({
                                                                                            message: response.msge,
                                                                                            size: 'large'
                                                                                        });
                                                                          }
                                                                        }
                                                                        else{
                                                                          bootbox.alert({
                                                                                            message: response.msge,
                                                                                            size: 'large'
                                                                                        });
                                                                        }
                                                     })
                                                     .error(function(error){
                                                                          bootbox.alert({
                                                                                            message: 'No se pudo cargar el articulo!!',
                                                                                            size: 'large'
                                                                                        });
                                                     });
                                              $(this).show();
                                              form.attr('action', url_old);
    
  });
  
							$('.btnsend').click(function(event){
							                           event.preventDefault();
									                       bootbox.confirm({
								                                            message: "Seguro enviar el formulario?",
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
								                                                    $('.frmsend').submit();
								                                                }
								                                            }
								                                        });     	
									});
							$('.btncancel').click(function(event){
							                           event.preventDefault();
									                       bootbox.confirm({
								                                            message: "Seguro cancelar el envio del formulario?",
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
								                                                    $('.frmcancel').submit();
								                                                }
								                                            }
								                                        });     	
									});	 
							$('.btnpausa').click(function(event){
							                           event.preventDefault();
									                       bootbox.confirm({
								                                            message: "Seguro poner en pausa el formulario?",
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
								                                                    $('.frmpaused').submit();
								                                                }
								                                            }
								                                        });     	
									});	 									
});

function removeItem(url, id){
                 bootbox.confirm({
                                    message: "Seguro quitar el item?",
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
                                            $.post(url,function(data){
                                                                      if (data.ok){
                                                                        $('#'+id).remove();
                                                                      }
                                            });
                                        }
                                    }
                                });     	
	}

function generateRow(item)
{
  return "<tr id='"+item.id+"'><td>"+item.codigo+"</td><td>"+item.descripcion+"</td><td>"+item.cantidad+"</td><td>"+item.unitario+"</td><td>"+item.total+"</td><td><input type='button' class='btn btn-warning btn-sm delit' value='X' onClick='removeItem(\""+item.url+"\", "+item.id+")';/></td></tr>";
}
