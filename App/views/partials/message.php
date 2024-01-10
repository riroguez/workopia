<?php 
    use Framework\Session;
?>

<?php $successMessage = Session::getFlashMessage('success_message'); ?>
<?php if($successMessage !== null) { ?>
    <div class="message bg-green-100 p-3 mb-3">
        <?php echo $successMessage; ?>
    </div>
<?php } ?>

<?php $errorMessage = Session::getFlashMessage('error_message'); ?>
<?php if($errorMessage !== null) { ?>
    <div class="message bg-red-100 p-3 mb-3">
        <?php echo $errorMessage; ?>
    </div>
<?php } ?>
