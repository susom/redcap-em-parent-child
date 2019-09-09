<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $this */

?>
<script src="<?php echo $this->getUrl('assets/js/form.js') ?>"></script>
<script>
    FormObject.addURL = "<?php  echo $_SERVER['REQUEST_SCHEME'] . '://' . SERVER_NAME . $this->getTopParentArm()->getUrl() ?>"
    FormObject.updateAddURL();

</script>
