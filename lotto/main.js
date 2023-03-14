function addLottoTicket(numbers) {
    const ticketsContainer = document.getElementById("tickets-container");

    const ticketElement = document.createElement("div");
    ticketElement.classList.add("lotto-ticket");

    const numbersContainer = document.createElement("div");
    numbersContainer.classList.add("numbers");

    for (let number of numbers) {
	    const numberElement = document.createElement("div");
	    numberElement.classList.add("number");
	    numberElement.textContent = number;
	    numbersContainer.appendChild(numberElement);
    }

    ticketElement.appendChild(numbersContainer);

    ticketsContainer.appendChild(ticketElement);
}

function updateJackpot(amount) {
    const jackpotAmountElement = document.querySelector(".jackpot-amount");
    jackpotAmountElement.textContent = `${amount} MEG-Taler`;
}

function startCountdown(endDate) {
  const countdownElement = document.querySelector(".countdown-timer");

  const countdownInterval = setInterval(() => {
    const now = new Date().getTime();
    const distance = endDate.getTime() - now;

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    countdownElement.textContent = `${days.toString().padStart(2, '0')}:${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

    if (distance < 0) {
        clearInterval(countdownInterval);
        countdownElement.textContent = "00:00:00:00";
    }
  }, 1000);
}

const endDate = new Date().getTime() + (2 * 24 * 60 * 60 * 1000) + (4 * 60 * 60 * 1000) + (30 * 60 * 1000) + (15 * 1000);
startCountdown(new Date(endDate));
