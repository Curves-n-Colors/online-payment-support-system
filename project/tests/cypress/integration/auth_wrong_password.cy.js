describe('Wrong Password', ()=> {
    it('Checks for wrong password.', ()=>{
        cy.visit("/login");
        cy.get(":nth-child(2) > .controls > .form-control").type(
            "info@thundercodes.com"
        );
        cy.get(":nth-child(3) > .controls > .form-control").type("Nepal123");
        cy.contains("button", "Sign in").click();
        cy.assertRedirect("/login");
    });
})