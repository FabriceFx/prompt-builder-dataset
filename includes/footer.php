<footer class="bg-white border-t border-slate-200 py-10 mt-auto">
    <div class="max-w-6xl mx-auto px-4 text-center">

        <div class="mb-6">
            <a href="https://paypal.me/FFaucheux" target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-50 text-slate-500 hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 border border-transparent transition-all duration-300 text-xs font-medium group">
                <i class="fas fa-mug-hot group-hover:animate-bounce"></i>
                <span>
                    <?php echo t('footer_tip'); ?>
                </span>
            </a>
        </div>

        <p class="text-slate-500 text-sm">
            ©
            <?php echo date('Y'); ?> <strong>Fabrice Faucheux</strong> —
            <?php echo t('footer_copy'); ?>
        </p>

        <div class="mt-4 flex justify-center gap-4">
            <a href="https://www.linkedin.com/in/fabricefaucheux" class="text-slate-400 hover:text-blue-600 transition"
                aria-label="LinkedIn"><i class="fab fa-linkedin text-xl"></i></a>
            <a href="https://atelier-informatique.com/" class="text-slate-400 hover:text-blue-600 transition"
                aria-label="Website"><i class="fas fa-globe text-xl"></i></a>
        </div>
    </div>
</footer>
</body>

</html>