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

