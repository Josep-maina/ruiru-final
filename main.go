package main

import (
    "fmt"
    "log"
    "net/http"
    "os"
)

func handler(w http.ResponseWriter, r *http.Request) {
    fmt.Fprintln(w, "Welcome to Ruiru Final App!")
}

func main() {
    port := os.Getenv("PORT")
    if port == "" {
        port = "8080" // fallback for local testing
    }

    http.HandleFunc("/", handler)

    log.Printf("Starting server on port %s...\n", port)
    err := http.ListenAndServe(":"+port, nil)
    if err != nil {
        log.Fatal(err)
    }
}
