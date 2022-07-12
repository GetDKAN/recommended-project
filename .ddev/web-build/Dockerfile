# Cypress.io requirements
# https://docs.cypress.io/guides/continuous-integration/introduction#Dependencies

ARG BASE_IMAGE
FROM $BASE_IMAGE

# Given that ddev has installed python3-pip, we can grab yq.
RUN pip3 install yq

# TODO We're currently using 8.7.0 of Cypress in our CI builds, but we should
#   update the tests to use 10+.
# TODO All packages with Cypress tests should specify their own version of
#   the framework, and should not expect a global binary.
# TODO Remove this once all the packages have their own specification.
RUN npm install --location=global cypress@8.7.0 && \
  npx cypress install

# TODO This method of adding Chromium supports Pa11y CI. We should investigate
#   re-adding these lines, or figuring out how to do it 'the right way.'
# Download chromium manually, because the package name varies between machine.
#RUN apt-get update && apt-get install -y chromium || apt-get install -y chromium-browser
# https://github.com/puppeteer/puppeteer/issues/7981 - If pa11y-ci updates their version of puppeteer to ~13, we can remove this symlink.
#RUN ln -s `which chromium` /usr/bin/chromium-browser
