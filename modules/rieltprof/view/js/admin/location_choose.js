$.allReady(function(){
   $('body').on('change', '.regionChooseFromWrapper', function(e){
       const select = $(e.target);
       const url = $(this).data('url');
       const chooseClass = $(this).data('choose');
       const choose = $(this).data('choose');
       const targetClass = $(this).data('target-class');
       const target = $(targetClass);
       console.log('select', select);
       console.log('url', url);
       console.log('choose', choose);
       console.log('targetClass', targetClass);
       $.ajax({
           type: 'POST',
           url: url,
           data: {id: select.val()},
           dataType: 'JSON',
           success: (res) => {
               console.log(res);
           },
           error: (err) => {
               console.error(err);
           }
       })
   });
});
