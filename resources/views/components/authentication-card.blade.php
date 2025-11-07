<div class="w-full max-w-4xl mx-auto bg-white/30 backdrop-blur-md border border-white/40 shadow-xl rounded-xl min-h-[28rem] md:min-h-[32rem]">
    <div class="flex flex-col md:flex-row items-center md:items-stretch gap-0 h-full min-h-[28rem] md:min-h-[32rem]">
        <!-- Logo Column -->
        <div class="hidden md:flex flex-col items-center justify-center md:w-1/2 w-full min-h-[28rem] md:min-h-[32rem] px-6 py-10">
            <img src="/images/usep.png" alt="USeP Logo" class="w-full max-w-[18rem] object-contain drop-shadow-lg" />
        </div>
        <!-- Form/Content Column -->
        <div class="flex-1 flex flex-col justify-center w-full px-8 md:pl-3 py-10">
            {{ $slot }}
        </div>
    </div>
</div>
