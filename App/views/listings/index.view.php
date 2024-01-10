<?php loadPartials('head'); ?>
<?php loadPartials('navbar'); ?>
<?php loadPartials('top-banner'); ?>

<!-- Job Listings -->
<section>
    <div class="container mx-auto p-4 mt-4">
        <div class="text-center text-3xl mb-4 font-bold border border-gray-300 p-3">
            <?php if($keywords) { ?>
                Search Results for: <?php echo sanitize($keywords); ?>
            <?php } else { ?>
                All Jobs
            <?php } ?>
        </div>

        <?php echo loadPartials('message'); ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <?php foreach ($listings as $list) { ?>
                <!-- Job Listing 1: Software Engineer -->
                <div class="rounded-lg shadow-md bg-white">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold"><?php echo $list->title; ?></h2>
                        <p class="text-gray-700 text-lg mt-2">
                            <?php echo $list->description; ?>
                        </p>
                        <ul class="my-4 bg-gray-100 p-4 rounded">
                            <li class="mb-2"><strong>Salary:</strong> <?php echo formattedSalary($list->salary); ?></li>
                            <li class="mb-2">
                                <strong>Location:</strong> <?php echo $list->city; ?>, <?php echo $list->state; ?>
                                <span class="text-xs bg-blue-500 text-white rounded-full px-2 py-1 ml-2">Local</span>
                            </li>
                            <li class="mb-2">
                                <strong>Tags:</strong> <?php echo $list->tags; ?>

                            </li>
                        </ul>
                        <a href="/listings/<?php echo $list->id; ?>" class="block w-full text-center px-5 py-2.5 shadow-sm rounded border text-base font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            Details
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
</section>

<?php loadPartials('botton-banner'); ?>
<?php loadPartials('footer'); ?>