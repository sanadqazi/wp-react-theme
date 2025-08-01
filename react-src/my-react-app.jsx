const App = () => {
    const [firstName, setFirstName] = React.useState('');
    const [lastName, setLastName] = React.useState('');
    const [phone, setPhone] = React.useState('');
    const [status, setStatus] = React.useState(null);

    const handleSubmit = async (e) => {
        e.preventDefault();

        const data = {
            whatsapp_number: phone.startsWith('+') ? phone : `+${phone}`,
            first_name: firstName,
            last_name: lastName
        };

        try {
            const res = await fetch('https://app.wanotifier.com/api/v1/contacts/?key=XNNDCPM9J7daeEQgi7c32qOQvXliV8', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!res.ok) {
                throw new Error('Failed to create contact');
            }

            setStatus('Contact created successfully!');
            setFirstName('');
            setLastName('');
            setPhone('');
        } catch (error) {
            console.error('Error:', error);
            setStatus('Error creating contact.');
        }
    };

    return (
        <div>
            <h2>Create Contact</h2>
            <form onSubmit={handleSubmit}>
                <p>
                    <label>First Name: <br />
                        <input type="text" value={firstName} onChange={e => setFirstName(e.target.value)} required />
                    </label>
                </p>
                <p>
                    <label>Last Name: <br />
                        <input type="text" value={lastName} onChange={e => setLastName(e.target.value)} required />
                    </label>
                </p>
                <p>
                    <label>Phone Number: <br />
                        <input type="text" value={phone} onChange={e => setPhone(e.target.value)} required />
                    </label>
                </p>
                <button type="submit">Submit</button>
            </form>
            {status && <p>{status}</p>}
        </div>
    );
};

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('react-app');
    if (container) {
        ReactDOM.render(<App />, container);
    }
});
