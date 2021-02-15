<select name="tg_filter_posts_value" onchange="tg_filter_changed()">
  <option value=""><?php
  _e( 'Filter by tag group', 'tag-groups' ); ?></option>
  <?php
  foreach ( $term_groups as $term_group => $label ) {
    printf( '<option value="%s"%s>%s</option>', $term_group, ( '' != $current_term_group && $term_group == $current_term_group ) ? ' selected="selected"' : '', htmlentities( $label, ENT_QUOTES, "UTF-8" ) );
  }
  ?>
</select>
<script>

function tg_filter_changed() {

  var selectedGroup = document.getElementsByName("tg_filter_posts_value")[0].value;

  if (selectedGroup == "") {
    document.getElementById("cat").removeAttribute("disabled");
  } else {
    document.getElementById("cat").setAttribute("disabled","");
  }
  return true;
}

function category_filter_changed() {

  var selectedGroup = document.getElementById("cat").value;

  if (selectedGroup == "0") {
    document.getElementsByName("tg_filter_posts_value")[0].removeAttribute("disabled");
  } else {
    document.getElementsByName("tg_filter_posts_value")[0].setAttribute("disabled","");
  }
  return true;
}

document.getElementById("cat").setAttribute("onchange","category_filter_changed()");

// initial settings

tg_filter_changed();

category_filter_changed();

</script>
