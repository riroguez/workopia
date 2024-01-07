<?php loadPartials('head'); ?>
<?php loadPartials('navbar'); ?>

<section>
    <div class="container mx-auto p-4 mt-4">
        <div class="text-center text-3xl mb-4 font-bold border border-gray-300 p-3"><?php echo $status; ?></div>
        <p class="text-center text-2xl mb-4">
            <?php echo $message; ?>
        </p>
        <a class="block text-center mt-4 text-blue-700" href="/listings">Go Back To</a>
    </div>
</section>

<?php loadPartials('footer'); ?>