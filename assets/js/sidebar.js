document.addEventListener('DOMContentLoaded', function () {
	const sidebar = document.querySelector('.sidebar');
	const content = document.querySelector('.content');

	sidebar.addEventListener('mouseenter', function () {
		sidebar.style.left = '0';
	});

	sidebar.addEventListener('mouseleave', function () {
		sidebar.style.left = '-250px';
	});
});
