(function() {

    document.body.onload = function() {

        setTimeout(function() {

            document.querySelector("#loading").classList.add('transition-all', 'duration-1000', 'opacity-0');

            setTimeout(function() {
                document.querySelector("#loading").classList.add('hidden');
            },1000);

        },300);

    }
})();
