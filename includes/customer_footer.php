<script src="component/js/jquery-2.1.4.js"></script>
<script>
(function(){
  var $ = window.jQuery;
  if(!$) return;
  $(function(){
    $('.container table.table').each(function(){
      var $t = $(this);
      if(!$t.parent().hasClass('table-responsive')){
        $t.wrap('<div class="table-responsive"></div>');
      }
    });
  });
})();
</script>
</body>
</html>