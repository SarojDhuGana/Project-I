
function increaseQuantity(inputId, stockValue) {
    var input = document.getElementById(inputId);
    var currentValue = parseInt(input.value);
    if (currentValue < stockValue) {
        input.value = currentValue + 1;
        document.getElementById('selected-' + inputId).value = currentValue + 1;
    }
}

function decreaseQuantity(inputId) {
    var input = document.getElementById(inputId);
    var value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
        document.getElementById('selected-' + inputId).value = value - 1;
    }
}

