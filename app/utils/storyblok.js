export function toStoryblokImage(url, width) {
  if (!url) return "";
  if (!url.includes("a.storyblok.com") && !url.includes("img2.storyblok.com"))
    return url;
  return `${url}/m/${width}x0/filters:quality(85):format(webp)`;
}
