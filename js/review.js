let stars = document.getElementsByClassName("star");

function gfg(n) {
    remove();
    const cls = ["one", "two", "three", "four", "five"][n - 1];
    for (let i = 0; i < n; i++) {
        stars[i].className = "star " + cls;
    }
    const input = document.getElementById("rating-value");
    if (input) input.value = n;
}

function remove() {
    for (let i = 0; i < 5; i++) {
        stars[i].className = "star";
    }
}