<?php if (isset($errors)) { ?>
    <?php foreach ($errors as $error) { ?>
        <div class="message bg-red-100 my-3 px-2 py-1 text-small">
            <?php echo $error; ?>
        </div>
    <?php } ?>
<?php } ?>