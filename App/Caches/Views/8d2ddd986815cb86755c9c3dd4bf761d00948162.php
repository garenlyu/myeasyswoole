

<?php $__env->startSection('example.index'); ?>
    <?php echo e($content); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
	new Vue({
		el: '#app',
		vuetify: new Vuetify(),
		data:{
        },
		created(){

		},
		watch:{
		},
	})
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/myeasyswoole/App/Views/example/index.blade.php ENDPATH**/ ?>