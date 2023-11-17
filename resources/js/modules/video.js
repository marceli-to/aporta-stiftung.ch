document.addEventListener('DOMContentLoaded', () => {

  if (!document.querySelector('[data-play-button]')) return;
  
  const playButton = document.querySelector('[data-play-button]');
  playButton.addEventListener('click', (e) => {
    e.preventDefault(); // Prevent default anchor behavior
    const videoDataUrl = playButton.dataset.videoUrl;
    const videoId = videoDataUrl.split('/').pop();
    const videoUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`; // Added autoplay and muted parameters

    const iframe = document.createElement('iframe');
    iframe.src = videoUrl;
    iframe.frameBorder = "0";
    iframe.setAttribute("allow", "autoplay; fullscreen"); // Added 'autoplay' to the 'allow' attribute
    iframe.setAttribute("allowfullscreen", "");
    iframe.classList.add('w-full', 'h-full', 'block', 'aspect-video'); // Tailwind classes for width and height

    const poster = document.querySelector('[data-poster]');
    poster.replaceWith(iframe); // Replace the poster image with the iframe
    playButton.remove(); // Remove the play button
  });
});