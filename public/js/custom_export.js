document.addEventListener('DOMContentLoaded', function () {
	let currentTime = 0;
	let interval = null;
	let statusInterval = null;

	const modalSpace = document.querySelector('.modal-space');
	if (modalSpace) {
		modalSpace.addEventListener('click', function () {
			closeModal();
		});
	}

	document.addEventListener('click', function (event) {
		const button = event.target.closest('[data-status-route]');
		if (!button) return;

		event.preventDefault();
		event.stopPropagation();

		button.setAttribute('disabled', '');

		fetch(button.getAttribute('href'))
			.then(response => response.json())
			.then(response => {
				const getFileRoute = button.getAttribute('data-file-route');

				if (response.status === 'in_progress') {
					initModal();
					initInterval();
					initListener(response.process_id, button.getAttribute('data-status-route'), getFileRoute);
					button.removeAttribute('disabled');
				} else if (response.status === 'success') {
					const separator = getFileRoute.includes('?') ? '&' : '?';
					downloadURI(`${getFileRoute}${separator}id=${response.process_id}`);
				}
			})
			.catch(error => {
				console.error('Error:', error);
				button.removeAttribute('disabled');
			});
	});

	const downloadURI = (uri, name) => {
		let link = document.createElement('a');
		link.setAttribute('download', '');
		link.href = uri;
		document.body.appendChild(link);
		link.click();
		link.remove();
	};

	const initInterval = () => {
		interval = setInterval(() => {
			currentTime++;
			const timeElement = document.querySelector('.modal-space .time');
			if (timeElement) {
				timeElement.textContent = currentTime;
			}
		}, 1000);
	};

	const resetInterval = () => {
		if (interval) {
			currentTime = 0;
			clearInterval(interval);
		}
	};

	const initModal = () => {
		const modalSpace = document.querySelector('.modal-space');
		if (!modalSpace) return;

		modalSpace.innerHTML = '<div class="modal-content">Please wait! <br> Time spent: <span><span class="time">1</span>s</span></div>';
		modalSpace.classList.add('open');

		const modalContent = modalSpace.querySelector('.modal-content');
		if (modalContent) {
			modalContent.addEventListener('click', function (event) {
				event.stopPropagation();
			});
		}
	};

	const closeModal = () => {
		currentTime = 0;
		const modalSpace = document.querySelector('.modal-space');
		if (modalSpace) {
			modalSpace.classList.remove('open');
			modalSpace.innerHTML = '';
		}
		resetInterval();
	};

	const initListener = (processId, checkStatusUrl, getFileRoute) => {
		if (statusInterval) {
			clearInterval(statusInterval);
		}

		statusInterval = setInterval(() => {
			const params = new URLSearchParams({ id: processId });

			fetch(`${checkStatusUrl}?${params}`)
				.then(response => response.json())
				.then(response => {
					if (response.status === 'success') {
						clearInterval(statusInterval);
						closeModal();
						const separator = getFileRoute.includes('?') ? '&' : '?';
						downloadURI(`${getFileRoute}${separator}id=${processId}`);
					} else if (response.status !== 'in_progress') {
						clearInterval(statusInterval);

						const modalContent = document.querySelector('.modal-content');
						if (modalContent) {
							modalContent.textContent = 'Error occur please call administrator!';
						}

						setTimeout(() => {
							closeModal();
						}, 3000);
					}
				})
				.catch(error => {
					console.error('Error:', error);
					clearInterval(statusInterval);
					closeModal();
				});
		}, 3000);
	};
});
